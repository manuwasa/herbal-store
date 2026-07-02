<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by a ShippingRateProvider on any non-success response or transport
 * failure. Caught in ShippingRateController and turned into the "arranged via
 * WhatsApp" fallback — never allowed to bubble into a 500 or block checkout.
 */
class ShippingRateException extends RuntimeException
{
}
