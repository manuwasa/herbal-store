<?php

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Session-backed cart. Storage shape: session('cart') = [product_id => quantity].
 * A stateful, request-scoped service (unlike the app's static builder services)
 * because a session-backed cart genuinely needs request/session state.
 */
class CartService
{
    private const KEY = 'cart';
    private const MAX_QTY = 99;

    public function __construct(private Request $request)
    {
    }

    public function add(Product $product, int $quantity): void
    {
        $cart = $this->raw();
        $current = $cart[$product->id] ?? 0;
        $cart[$product->id] = min($current + $quantity, self::MAX_QTY);
        $this->put($cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min($quantity, self::MAX_QTY);
        }

        $this->put($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->put($cart);
    }

    public function clear(): void
    {
        $this->request->session()->forget(self::KEY);
    }

    /**
     * @return Collection<int, CartLine>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function (int $qty, int $productId) use ($products) {
                $product = $products->get($productId);

                return $product ? new CartLine($product, $qty) : null;
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function subtotal(): string
    {
        return $this->items()->reduce(
            fn (string $carry, CartLine $line) => bcadd($carry, $line->lineTotal(), 2),
            '0'
        );
    }

    public function totalWeightGrams(): int
    {
        return $this->items()->reduce(
            fn (int $carry, CartLine $line) => $carry + (($line->product->weight ?? 0) * $line->quantity),
            0
        );
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return array<int, int>
     */
    private function raw(): array
    {
        return $this->request->session()->get(self::KEY, []);
    }

    /**
     * @param  array<int, int>  $cart
     */
    private function put(array $cart): void
    {
        $this->request->session()->put(self::KEY, $cart);
    }
}
