<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when an order status transition is attempted from an invalid
 * prior state (e.g. jumping straight to "shipped" on an unpaid order).
 * A hard backstop against forged/replayed admin requests, not user guidance.
 */
class InvalidOrderTransitionException extends RuntimeException
{
}
