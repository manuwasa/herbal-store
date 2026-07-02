<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by CheckoutService when the cart can no longer be fulfilled at
 * order-creation time (a product went inactive, or the resolved branch's
 * stock no longer covers the requested quantity). The checkout controller
 * catches this and re-renders with a buyer-facing error message.
 */
class CartValidationException extends RuntimeException
{
}
