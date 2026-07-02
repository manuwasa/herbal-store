<?php

namespace App\View\Composers;

use App\Services\Cart\CartService;
use Illuminate\View\View;

/**
 * Shares the cart item count to the navbar on every public page — read straight
 * from the session via CartService, so it's zero-query per pageview.
 */
class CartComposer
{
    public function __construct(private CartService $cart)
    {
    }

    public function compose(View $view): void
    {
        $view->with('cartCount', $this->cart->count());
    }
}
