<?php

namespace App\Services\Cart;

use App\Models\Product;

class CartLine
{
    public function __construct(
        public readonly Product $product,
        public readonly int $quantity,
    ) {
    }

    public function lineTotal(): string
    {
        return bcmul((string) $this->product->price, (string) $this->quantity, 2);
    }
}
