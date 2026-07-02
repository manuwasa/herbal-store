<?php

namespace App\Services\Payments;

/**
 * What a gateway returns after creating a payment transaction — everything the
 * frontend needs to actually collect payment. For Midtrans Snap: a token + a
 * redirect URL.
 */
class PaymentTransactionResult
{
    public function __construct(
        public readonly string $snapToken,
        public readonly ?string $redirectUrl,
        public readonly string $gatewayOrderId,
        public readonly array $rawResponse = [],
    ) {
    }
}
