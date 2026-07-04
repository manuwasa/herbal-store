<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_order_id',
        'status',
        'payment_type',
        'gross_amount',
        'snap_token',
        'raw_payload',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'raw_payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * `status` is stored verbatim from the gateway's own vocabulary (Midtrans
     * transaction_status), plus two values this app writes itself on a
     * refund ('refund' on success, 'refund_failed' when the gateway rejects
     * the automatic refund attempt — see Order::handleRefund()). This is a
     * display label only, not a domain enum, since the raw value must stay
     * exactly what the gateway sent for auditing.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'capture' => 'Diproses',
            'settlement' => 'Berhasil',
            'deny' => 'Ditolak',
            'cancel' => 'Dibatalkan',
            'expire' => 'Kedaluwarsa',
            'refund' => 'Refund Berhasil',
            'partial_refund' => 'Refund Sebagian',
            'refund_failed' => 'Refund Gagal',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'settlement' => 'bg-green-100 text-green-700',
            'pending', 'capture' => 'bg-amber-100 text-amber-700',
            'deny', 'refund_failed' => 'bg-red-100 text-red-700',
            'cancel', 'expire' => 'bg-gray-100 text-gray-500',
            'refund', 'partial_refund' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-gray-100 text-gray-500',
        };
    }
}
