<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Payments\PaymentGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessMidtransNotification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload  Already signature-verified by the controller.
     */
    public function __construct(private array $payload)
    {
    }

    public function handle(PaymentGateway $gateway): void
    {
        $notification = $gateway->parseWebhookNotification($this->payload);

        // gateway_order_id is "{public_reference}-{attempt}" — strip the suffix.
        $publicReference = Str::beforeLast($notification->gatewayOrderId, '-');
        $order = Order::query()->where('public_reference', $publicReference)->first();

        if (! $order) {
            Log::warning('Midtrans webhook for unknown order', [
                'gateway_order_id' => $notification->gatewayOrderId,
            ]);

            return;
        }

        DB::transaction(function () use ($order, $notification, $gateway) {
            // Idempotency guard #1 — exact-duplicate (same transaction + status) is a no-op.
            $alreadyProcessed = $order->paymentTransactions()
                ->where('gateway_transaction_id', $notification->gatewayTransactionId)
                ->where('status', $notification->status)
                ->exists();

            if ($alreadyProcessed) {
                return;
            }

            $order->paymentTransactions()->create([
                'gateway' => $gateway->identifier(),
                'gateway_transaction_id' => $notification->gatewayTransactionId,
                'gateway_order_id' => $notification->gatewayOrderId,
                'status' => $notification->status,
                'payment_type' => $notification->paymentType,
                'gross_amount' => $notification->grossAmount,
                'raw_payload' => $notification->raw,
                'processed_at' => now(),
            ]);

            // A refund event is the one webhook that acts on an already-paid order.
            if ($notification->isRefund()) {
                $order->markAsCancelled(null, 'Refund dari gateway', attemptRefund: false);

                return;
            }

            // Idempotency guard #2 — only advance a still-pending order (forward-only).
            if ($order->status !== \App\Enums\OrderStatus::PendingPayment) {
                return;
            }

            match ($notification->mappedOrderStatus) {
                \App\Enums\OrderStatus::Paid => tap($order)->markAsPaid()->forceFill([
                    'payment_method' => $gateway->identifier(),
                    'payment_channel' => $notification->paymentType,
                ])->save(),
                \App\Enums\OrderStatus::Expired => $order->markAsExpired(),
                \App\Enums\OrderStatus::Cancelled => $order->markAsCancelled(null, 'Dibatalkan gateway', attemptRefund: false),
                default => null,
            };
        });
    }
}
