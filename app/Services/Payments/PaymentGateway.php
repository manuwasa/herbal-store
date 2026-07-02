<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * Register a payment transaction with the gateway and return everything the
     * frontend needs to collect payment (Snap token + redirect URL).
     */
    public function createTransaction(Order $order): PaymentTransactionResult;

    /**
     * Verify an inbound webhook payload's signature is authentic. Must be called
     * before any other webhook logic trusts the payload.
     */
    public function verifyWebhookSignature(array $payload): bool;

    /**
     * Translate a verified webhook payload into a normalized notification.
     */
    public function parseWebhookNotification(array $payload): WebhookNotification;

    /**
     * Refund a paid order in full. A channel that can't be refunded via API
     * returns RefundResult(success: false, errorMessage: ...), not an exception.
     */
    public function refund(Order $order, string $reason): RefundResult;

    /**
     * Gateway identifier stored on payment_transactions.gateway / orders.payment_method.
     */
    public function identifier(): string;
}
