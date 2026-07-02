<?php

namespace App\Http\Controllers;

use App\Exceptions\CartValidationException;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Payments\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('checkout.index', [
            'setting' => Setting::current(),
            'cartItems' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(Request $request, CheckoutService $checkout)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'customer_email' => ['nullable', 'email:rfc', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'privacy_consent' => ['accepted'],
            // Shipping selection identifiers (never a trusted price).
            'shipping_area_id' => ['nullable', 'string', 'max:100'],
            'shipping_area_label' => ['nullable', 'string', 'max:255'],
            'shipping_courier' => ['nullable', 'string', 'max:50'],
            'shipping_service' => ['nullable', 'string', 'max:50'],
            'shipping_service_label' => ['nullable', 'string', 'max:150'],
            'recaptcha_token' => ['nullable', 'string'],
        ]);

        if (! $this->passesRecaptcha($request)) {
            return back()->withInput()->withErrors(['recaptcha' => 'Verifikasi keamanan gagal, silakan coba lagi.']);
        }

        try {
            $order = $checkout->createOrder($data, $request->session()->getId());
        } catch (CartValidationException $e) {
            return redirect()->route('cart.index')->withErrors(['cart' => $e->getMessage()]);
        }

        return redirect()->route('checkout.pay', $order);
    }

    public function pay(Order $order, PaymentGateway $gateway)
    {
        // Only a freshly-created, unpaid order can be paid.
        abort_unless($order->status === \App\Enums\OrderStatus::PendingPayment, 404);

        $result = $gateway->createTransaction($order);

        return view('checkout.pay', [
            'setting' => Setting::current(),
            'order' => $order,
            'snapToken' => $result->snapToken,
            'snapClientKey' => Setting::current()->midtrans_client_key,
            'isProduction' => Setting::current()->midtrans_is_production,
        ]);
    }

    public function confirmation(Order $order)
    {
        return view('checkout.confirmation', [
            'setting' => Setting::current(),
            'order' => $order->load('items', 'branch'),
        ]);
    }

    /**
     * reCAPTCHA v3 — invisible, score-based. Never hard-blocks if Google's own
     * API is unreachable (same "third-party outage never blocks a sale" rule as
     * shipping). Skipped entirely when disabled/unconfigured.
     */
    private function passesRecaptcha(Request $request): bool
    {
        $setting = Setting::current();

        if (! $setting->recaptcha_enabled || ! $setting->hasRecaptchaSecretKey()) {
            return true;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $setting->recaptcha_secret_key,
                'response' => $request->input('recaptcha_token'),
                'remoteip' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return true; // Google unreachable — don't block the sale.
        }

        $body = $response->json();

        if (! is_array($body) || ! ($body['success'] ?? false)) {
            return false;
        }

        // v3 returns a 0..1 score; 0.5 is Google's commonly-recommended cutoff.
        return ($body['score'] ?? 0) >= 0.5;
    }
}
