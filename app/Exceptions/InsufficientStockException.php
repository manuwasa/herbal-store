<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by StockTransferService when a transfer would drive a source
 * branch's stock negative. Caught by the admin controller and surfaced
 * as a validation error.
 */
class InsufficientStockException extends RuntimeException
{
}
