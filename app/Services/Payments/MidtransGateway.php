<?php

namespace App\Services\Payments;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MidtransGateway implements PaymentGateway
{
    public function __construct(private Setting $setting)
    {
    }

    public function identifier(): string
    {
        return 'midtrans';
    }

    public function createTransaction(Order $order): PaymentTransactionResult
    {
        // Midtrans rejects re-using a previously-used order_id on a retry, so we
        // suffix with the attempt number while staying traceable to the order.
        $attempt = $order->paymentTransactions()->count() + 1;
        $gatewayOrderId = $order->public_reference . '-' . $attempt;

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->post($this->snapBaseUrl() . '/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $gatewayOrderId,
                    'gross_amount' => (int) round($order->total),
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'email' => $order->customer_email,
                ],
                'item_details' => $order->items->map(fn ($item) => [
                    'id' => (string) $item->product_id,
                    'price' => (int) round($item->unit_price),
                    'quantity' => $item->quantity,
                    'name' => Str::limit($item->product_name, 50, ''),
                ])->push([
                    'id' => 'SHIPPING',
                    'price' => (int) round($order->shipping_cost),
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ])->all(),
                'expiry' => [
                    // Match the order's own 2-hour auto-cancel window exactly.
                    'unit' => 'minute',
                    'duration' => 120,
                ],
            ])
            ->throw()
            ->json();

        return new PaymentTransactionResult(
            snapToken: $response['token'],
            redirectUrl: $response['redirect_url'] ?? null,
            gatewayOrderId: $gatewayOrderId,
            rawResponse: $response,
        );
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $this->serverKey()
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    public function parseWebhookNotification(array $payload): WebhookNotification
    {
        return new WebhookNotification(
            gatewayOrderId: $payload['order_id'] ?? '',
            gatewayTransactionId: $payload['transaction_id'] ?? '',
            status: $payload['transaction_status'] ?? '',
            mappedOrderStatus: $this->mapStatus(
                $payload['transaction_status'] ?? '',
                $payload['fraud_status'] ?? null
            ),
            paymentType: $payload['payment_type'] ?? null,
            grossAmount: $payload['gross_amount'] ?? null,
            raw: $payload,
        );
    }

    public function refund(Order $order, string $reason): RefundResult
    {
        $transactionId = $order->latestPaymentTransaction?->gateway_transaction_id;

        if (blank($transactionId)) {
            return new RefundResult(
                success: false,
                errorMessage: 'Tidak ada transaksi pembayaran untuk direfund.',
            );
        }

        try {
            $response = Http::withBasicAuth($this->serverKey(), '')
                ->acceptJson()
                ->post($this->apiBaseUrl() . "/v2/{$transactionId}/refund", [
                    'amount' => (int) round($order->total),
                    'reason' => Str::limit($reason, 200, ''),
                ]);

            $body = $response->json() ?? [];

            // Midtrans returns status_code "200"/"201" on a successful refund.
            $ok = $response->successful() && in_array((string) ($body['status_code'] ?? ''), ['200', '201'], true);

            return new RefundResult(
                success: $ok,
                refundId: $body['refund_key'] ?? ($body['transaction_id'] ?? null),
                rawResponse: $body,
                errorMessage: $ok ? null : ($body['status_message'] ?? 'Refund ditolak gateway.'),
            );
        } catch (\Throwable $e) {
            return new RefundResult(
                success: false,
                errorMessage: 'Gagal menghubungi gateway untuk refund: ' . $e->getMessage(),
            );
        }
    }

    private function mapStatus(string $transactionStatus, ?string $fraudStatus): OrderStatus
    {
        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => OrderStatus::Paid,
            $transactionStatus === 'settlement' => OrderStatus::Paid,
            in_array($transactionStatus, ['deny', 'cancel'], true) => OrderStatus::Cancelled,
            $transactionStatus === 'expire' => OrderStatus::Expired,
            in_array($transactionStatus, ['refund', 'partial_refund'], true) => OrderStatus::Cancelled,
            default => OrderStatus::PendingPayment, // pending, capture+challenge, unknown
        };
    }

    private function serverKey(): string
    {
        return (string) $this->setting->midtrans_server_key;
    }

    private function snapBaseUrl(): string
    {
        return $this->setting->midtrans_is_production
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    private function apiBaseUrl(): string
    {
        return $this->setting->midtrans_is_production
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
