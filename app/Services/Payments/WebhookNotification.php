<?php

namespace App\Services\Payments;

use App\Enums\OrderStatus;

/**
 * A gateway webhook payload, normalized into what this app understands —
 * without yet mutating anything.
 */
class WebhookNotification
{
    public function __construct(
        public readonly string $gatewayOrderId,
        public readonly string $gatewayTransactionId,
        public readonly string $status,          // raw gateway status
        public readonly OrderStatus $mappedOrderStatus,
        public readonly ?string $paymentType,
        public readonly ?string $grossAmount,
        public readonly array $raw = [],
    ) {
    }

    public function isRefund(): bool
    {
        return in_array($this->status, ['refund', 'partial_refund'], true);
    }
}
