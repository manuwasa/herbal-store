<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\Payments\MidtransGateway;
use App\Services\Payments\PaymentGateway;
use App\Services\Shipping\BiteshipProvider;
use App\Services\Shipping\ShippingRateProvider;
use App\View\Composers\CartComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Payment gateway abstraction — one impl now; a second provider later is
        // a match() arm here, zero controller changes.
        $this->app->bind(PaymentGateway::class, fn () => new MidtransGateway(Setting::current()));

        // Shipping-rate abstraction — same pattern.
        $this->app->bind(ShippingRateProvider::class, fn () => new BiteshipProvider(Setting::current()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        // Checkout creates a permanent order row + calls Midtrans per hit.
        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));

        // Cart mutations.
        RateLimiter::for('cart-write', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        // Biteship-backed area search + rate lookup (billed usage — cost control).
        RateLimiter::for('shipping-lookup', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        // Webhook — defense in depth; signature verification is the real gate.
        RateLimiter::for('webhook-midtrans', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        // Navbar cart badge — zero-query, reads the session via CartService.
        View::composer('components.navbar', CartComposer::class);
    }
}
