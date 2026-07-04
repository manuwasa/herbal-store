<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index()
    {
        return view('cart.index', [
            'setting' => Setting::current(),
            'cartItems' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->findOrFail($data['product_id']);

        if (! $product->isPurchasable()) {
            $message = 'Produk ini sedang tidak tersedia.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['cart' => $message]);
        }

        $this->cart->add($product, (int) $data['quantity']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk ditambahkan ke keranjang.',
                'cartCount' => $this->cart->count(),
            ]);
        }

        // No-JS fallback: stay on the page the buyer was browsing, don't force
        // navigation to the cart — matches the JS-driven behavior's intent.
        return back()->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($product->id, (int) $data['quantity']);

        if ($request->wantsJson()) {
            $line = $this->cart->items()->first(fn ($l) => $l->product->id === $product->id);

            return response()->json([
                'success' => true,
                'removed' => is_null($line),
                'quantity' => $line?->quantity ?? 0,
                'lineTotal' => $line ? (float) $line->lineTotal() : 0,
                'subtotal' => (float) $this->cart->subtotal(),
                'cartCount' => $this->cart->count(),
                'isEmpty' => $this->cart->isEmpty(),
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Keranjang diperbarui.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->cart->remove($product->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'subtotal' => (float) $this->cart->subtotal(),
                'cartCount' => $this->cart->count(),
                'isEmpty' => $this->cart->isEmpty(),
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }
}
