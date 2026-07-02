<?php

namespace App\Services\Payments;

/**
 * Outcome of a refund attempt. A channel that doesn't support API refund is an
 * expected, named failure (success=false + errorMessage), not an exception.
 */
class RefundResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $refundId = null,
        public readonly array $rawResponse = [],
        public readonly ?string $errorMessage = null,
    ) {
    }
}
