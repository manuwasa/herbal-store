<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Services\Invoicing\InvoiceNumberGenerator;
use App\Services\Payments\PaymentGateway;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * `status` and `branch_id` are deliberately NOT fillable — they change only
     * through the controlled transition methods below, never a mass-assigned
     * array built from request input. `public_reference` and the transition
     * timestamps are set internally too.
     */
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'customer_note',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_channel',
        'courier_name',
        'tracking_number',
        'shipping_area_id',
        'shipping_area_label',
        'shipping_courier',
        'shipping_service',
        'shipping_service_label',
        'shipping_weight_grams',
        'privacy_consent_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'privacy_consent_at' => 'datetime',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->public_reference ??= (string) Str::uuid();
            // Set here (not via $fillable) so the in-memory instance is correct
            // immediately after create(), not only after a reload — while still
            // keeping status un-mass-assignable from request input.
            $order->status ??= OrderStatus::PendingPayment;
            // ??= lets OrderSeeder pre-assign a historically-backdated number
            // (via direct property assignment, not mass assignment) before save().
            $order->invoice_number ??= app(InvoiceNumberGenerator::class)->generate();
        });
    }

    // ---- Relations ------------------------------------------------------

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latestPaymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function shippingRateLookup(): HasOne
    {
        return $this->hasOne(ShippingRateLookup::class);
    }

    // ---- Scopes ---------------------------------------------------------

    public function scopeStatus(Builder $query, OrderStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Owner sees everything; branch staff sees only their own branch's orders.
     * A local scope, applied explicitly at call sites (never a global scope).
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->role === \App\Enums\UserRole::Owner) {
            return $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }

    // ---- Transitions (the ONLY sanctioned way status/branch_id change) ---

    public function markAsPaid(?User $changedBy = null): void
    {
        if ($this->status === OrderStatus::Paid) {
            return; // idempotent
        }

        $this->assertFrom([OrderStatus::PendingPayment]);

        DB::transaction(function () use ($changedBy) {
            $from = $this->status;
            $this->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();
            $this->recordHistory($from, OrderStatus::Paid, $changedBy);

            foreach ($this->items as $item) {
                $item->product?->decrementStockAt($this->branch, $item->quantity);
            }
        });
    }

    public function markAsExpired(?User $changedBy = null): void
    {
        if ($this->status === OrderStatus::Expired) {
            return;
        }

        $this->assertFrom([OrderStatus::PendingPayment]);

        DB::transaction(function () use ($changedBy) {
            $from = $this->status;
            $this->forceFill(['status' => OrderStatus::Expired])->save();
            $this->recordHistory($from, OrderStatus::Expired, $changedBy);
        });
    }

    public function markAsProcessing(User $changedBy): void
    {
        if ($this->status === OrderStatus::Processing) {
            return;
        }

        $this->assertFrom([OrderStatus::Paid]);

        DB::transaction(function () use ($changedBy) {
            $from = $this->status;
            $this->forceFill(['status' => OrderStatus::Processing])->save();
            $this->recordHistory($from, OrderStatus::Processing, $changedBy);
        });
    }

    public function markAsShipped(User $changedBy, string $courier, string $tracking): void
    {
        if ($this->status === OrderStatus::Shipped) {
            return;
        }

        $this->assertFrom([OrderStatus::Processing]);

        DB::transaction(function () use ($changedBy, $courier, $tracking) {
            $from = $this->status;
            $this->forceFill([
                'status' => OrderStatus::Shipped,
                'shipped_at' => now(),
                'courier_name' => $courier,
                'tracking_number' => $tracking,
            ])->save();
            $this->recordHistory($from, OrderStatus::Shipped, $changedBy, "Resi: {$courier} {$tracking}");
        });
    }

    public function markAsCompleted(User $changedBy): void
    {
        if ($this->status === OrderStatus::Completed) {
            return;
        }

        $this->assertFrom([OrderStatus::Shipped]);

        DB::transaction(function () use ($changedBy) {
            $from = $this->status;
            $this->forceFill(['status' => OrderStatus::Completed, 'completed_at' => now()])->save();
            $this->recordHistory($from, OrderStatus::Completed, $changedBy);
        });
    }

    /**
     * Cancel an order. On a previously-paid order this also refunds and restores
     * stock. $attemptRefund is false only when a Midtrans `refund` webhook is the
     * trigger (money already moved externally — re-refunding would be wrong).
     */
    public function markAsCancelled(?User $changedBy = null, ?string $note = null, bool $attemptRefund = true): void
    {
        if ($this->status === OrderStatus::Cancelled) {
            return;
        }

        $this->assertFrom([OrderStatus::PendingPayment, OrderStatus::Paid, OrderStatus::Processing]);

        $wasPaid = in_array($this->status, [OrderStatus::Paid, OrderStatus::Processing], true);

        DB::transaction(function () use ($changedBy, $note, $attemptRefund, $wasPaid) {
            $from = $this->status;

            $this->forceFill([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $note,
            ])->save();

            if ($wasPaid) {
                $this->handleRefund($attemptRefund, $note);

                foreach ($this->items as $item) {
                    $item->product?->restoreStockAt($this->branch, $item->quantity);
                }
            }

            $this->recordHistory($from, OrderStatus::Cancelled, $changedBy, $note);
        });
    }

    /**
     * Move fulfillment to a different branch after creation. Stock-safe: restores
     * at the old branch and decrements at the new one only if the order was paid
     * (i.e. stock was actually committed). Logged like any status change.
     */
    public function reassignBranch(User $changedBy, Branch $newBranch): void
    {
        $oldBranch = $this->branch;
        if ($oldBranch && $oldBranch->id === $newBranch->id) {
            return;
        }

        $stockCommitted = in_array($this->status, [OrderStatus::Paid, OrderStatus::Processing], true);

        DB::transaction(function () use ($changedBy, $newBranch, $oldBranch, $stockCommitted) {
            if ($stockCommitted) {
                foreach ($this->items as $item) {
                    if (! $item->product) {
                        continue;
                    }
                    if ($item->product->stockAt($newBranch) < $item->quantity) {
                        throw new InvalidOrderTransitionException(
                            "Cabang {$newBranch->name} tidak memiliki stok cukup untuk memindahkan pesanan ini."
                        );
                    }
                }

                foreach ($this->items as $item) {
                    $item->product?->restoreStockAt($oldBranch, $item->quantity);
                    $item->product?->decrementStockAt($newBranch, $item->quantity);
                }
            }

            $oldName = $oldBranch?->name ?? '—';
            $this->forceFill(['branch_id' => $newBranch->id])->save();
            $this->recordHistory(
                $this->status,
                $this->status,
                $changedBy,
                "Cabang dipindah dari {$oldName} ke {$newBranch->name}"
            );
        });
    }

    // ---- Internals ------------------------------------------------------

    private function assertFrom(array $allowed): void
    {
        if (! in_array($this->status, $allowed, true)) {
            throw new InvalidOrderTransitionException(
                "Tidak bisa mengubah status pesanan dari '{$this->status->value}'."
            );
        }
    }

    private function recordHistory(OrderStatus $from, OrderStatus $to, ?User $changedBy, ?string $note = null): void
    {
        $this->statusHistories()->create([
            'from_status' => $from->value,
            'to_status' => $to->value,
            'changed_by' => $changedBy?->id,
            'note' => $note,
            'created_at' => now(),
        ]);
    }

    private function handleRefund(bool $attemptRefund, ?string $note): void
    {
        if (! $attemptRefund) {
            // Refund already actioned externally (Midtrans dashboard) — just record it.
            $this->forceFill([
                'refund_status' => 'refunded',
                'refunded_at' => now(),
            ])->save();

            return;
        }

        $result = app(PaymentGateway::class)->refund($this, $note ?? 'Pesanan dibatalkan');

        if ($result->success) {
            $this->forceFill([
                'refund_status' => 'refunded',
                'refund_id' => $result->refundId,
                'refunded_at' => now(),
            ])->save();
        } else {
            // Order is still cancelled; refund_status: failed flags manual follow-up.
            $this->forceFill(['refund_status' => 'failed'])->save();
        }

        $this->paymentTransactions()->create([
            'gateway' => app(PaymentGateway::class)->identifier(),
            'gateway_transaction_id' => $result->refundId,
            'status' => $result->success ? 'refund' : 'refund_failed',
            'payment_type' => 'refund',
            'gross_amount' => $this->total,
            'raw_payload' => $result->rawResponse,
            'processed_at' => now(),
        ]);
    }
}
