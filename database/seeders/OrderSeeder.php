<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    private const COURIERS = [
        'JNE' => ['REG' => 'Reguler', 'YES' => 'Express'],
        'J&T Express' => ['EZ' => 'Ekonomi', 'REG' => 'Reguler'],
        'SiCepat' => ['REG' => 'Reguler', 'BEST' => 'Express'],
        'AnterAja' => ['REG' => 'Reguler'],
        'Ninja Xpress' => ['STANDARD' => 'Reguler'],
    ];

    private const PAYMENT_CHANNELS = ['qris', 'bank_transfer', 'gopay', 'shopeepay', 'credit_card'];

    /**
     * A believable order/transaction history so the admin dashboard, Laporan
     * Penjualan, and Riwayat Transaksi aren't empty on a fresh install. Every
     * status transition goes through the real Order model methods (never a
     * raw insert) so stock, history rows, and guarded fields stay consistent
     * — only the resulting timestamps are backdated afterward for a spread-
     * out, realistic-looking timeline.
     */
    public function run(): void
    {
        if (Order::query()->exists()) {
            return;
        }

        $branches = Branch::query()->active()->get();
        $products = Product::query()->active()->get();
        $owner = User::query()->where('role', UserRole::Owner->value)->first();

        if ($branches->isEmpty() || $products->isEmpty() || ! $owner) {
            return;
        }

        $staffByBranch = User::query()->where('role', UserRole::BranchStaff->value)->get()->keyBy('branch_id');

        // [status, count, min days ago, max days ago]
        $plan = [
            [OrderStatus::Completed, 32, 10, 50],
            [OrderStatus::Shipped, 16, 5, 18],
            [OrderStatus::Processing, 10, 2, 8],
            [OrderStatus::Paid, 8, 0, 3],
            [OrderStatus::PendingPayment, 6, 0, 0],
            [OrderStatus::Expired, 4, 6, 45],
            [OrderStatus::Cancelled, 4, 4, 40],
        ];

        foreach ($plan as [$status, $count, $minDaysAgo, $maxDaysAgo]) {
            for ($i = 0; $i < $count; $i++) {
                $this->seedOrder($status, $minDaysAgo, $maxDaysAgo, $branches, $products, $owner, $staffByBranch);
            }
        }
    }

    private function seedOrder(
        OrderStatus $targetStatus,
        int $minDaysAgo,
        int $maxDaysAgo,
        $branches,
        $products,
        User $owner,
        $staffByBranch
    ): void {
        $branch = $branches->random();
        $admin = $staffByBranch->get($branch->id) ?? $owner;

        $placedAt = $targetStatus === OrderStatus::PendingPayment
            ? now()->subMinutes(fake()->numberBetween(5, 90))
            : $this->randomBusinessMoment($minDaysAgo, $maxDaysAgo);

        // Statuses at or beyond Paid actually decrement live branch stock via
        // markAsPaid() — pick only from what that branch currently has, and
        // clamp quantity to it, so a low-stock combo never drives the
        // unsigned `stock` column negative. Never-paid statuses don't touch
        // stock at all, so any active product is fair game there.
        $willDecrementStock = ! in_array($targetStatus, [OrderStatus::PendingPayment, OrderStatus::Expired, OrderStatus::Cancelled], true);

        $candidates = $willDecrementStock
            ? $products->filter(fn (Product $p) => $p->stockAt($branch) > 0)->shuffle()
            : $products->shuffle();

        if ($willDecrementStock && $candidates->isEmpty()) {
            // Astronomically unlikely given seeded stock volumes, but never
            // let a lucky-unlucky draw crash the whole seeder run.
            $fallback = $products->random();
            $fallback->restoreStockAt($branch, 50);
            $candidates = collect([$fallback]);
        }

        $items = $candidates->take(fake()->numberBetween(1, min(3, $candidates->count())));
        $subtotal = 0;
        $weightGrams = 0;
        $lines = [];

        foreach ($items as $product) {
            $quantity = $willDecrementStock
                ? min(fake()->numberBetween(1, 3), $product->stockAt($branch))
                : fake()->numberBetween(1, 3);
            $lineTotal = $product->price * $quantity;
            $subtotal += $lineTotal;
            $weightGrams += $product->weight * $quantity;

            $lines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'unit_price' => $product->price,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        $shippingCost = 9000 + (int) (ceil($weightGrams / 1000) * 2500);
        [$courier, $service, $serviceLabel] = $this->randomCourier();

        $customer = fake('id_ID');
        $district = $customer->city();

        $order = new Order([
            'customer_name' => $customer->name(),
            'customer_phone' => $this->randomPhone(),
            'customer_email' => fake()->boolean(60) ? fake()->safeEmail() : null,
            'shipping_address' => $customer->streetAddress() . ', ' . $district,
            'customer_note' => fake()->boolean(20) ? fake()->sentence(6) : null,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'shipping_area_id' => null,
            'shipping_area_label' => "Kecamatan {$district}, {$branch->province_name}",
            'shipping_courier' => $courier,
            'shipping_service' => $service,
            'shipping_service_label' => $serviceLabel,
            'shipping_weight_grams' => $weightGrams,
            'privacy_consent_at' => $placedAt,
            'expires_at' => $placedAt->copy()->addHours(2),
        ]);

        // Pre-assign an invoice number dated to $placedAt (direct property
        // assignment, not mass assignment) so demo orders get a historically-
        // consistent INV/{year}/{month}/xxxx instead of every one of them
        // landing in whatever month the seeder actually runs in — the
        // creating hook's `??=` skips generating a fresh one since this is
        // already set.
        $order->invoice_number = app(\App\Services\Invoicing\InvoiceNumberGenerator::class)->generate($placedAt);

        $order->save();

        // branch_id is guarded — set directly like reassignBranch() does.
        $order->forceFill(['branch_id' => $branch->id])->save();
        $order->items()->createMany($lines);

        $lastEventAt = $this->advanceTo($order, $targetStatus, $placedAt, $admin, $courier, $service);

        // Backdate creation last. $lastEventAt may already equal the current
        // updated_at (the last transition step set it to the same value),
        // which Eloquent's dirty-check would treat as unchanged and then
        // auto-touch to the real current time on save() — so timestamps are
        // disabled for this one write to guarantee both columns land exactly
        // as given.
        $order->timestamps = false;
        $order->forceFill(['created_at' => $placedAt, 'updated_at' => $lastEventAt])->save();
        $order->timestamps = true;
    }

    /**
     * Drives the order through its real transition methods up to $target,
     * backdating every resulting timestamp (order columns + its own history
     * row) along the way. Returns the timestamp of the last event applied,
     * so the caller can set a consistent final `updated_at`.
     */
    private function advanceTo(Order $order, OrderStatus $target, Carbon $placedAt, User $admin, string $courier, string $service): Carbon
    {
        if ($target === OrderStatus::PendingPayment) {
            return $placedAt;
        }

        if ($target === OrderStatus::Expired) {
            $expiredAt = $placedAt->copy()->addHours(2);
            $order->markAsExpired();
            $order->forceFill(['updated_at' => $expiredAt])->save();
            $this->stampLatestHistory($order, $expiredAt);

            return $expiredAt;
        }

        if ($target === OrderStatus::Cancelled) {
            // Cancelled while still pending — never paid, so no refund attempt.
            $cancelledAt = $placedAt->copy()->addMinutes(fake()->numberBetween(10, 90));
            $order->markAsCancelled(null, 'Dibatalkan oleh pembeli sebelum pembayaran');
            $order->forceFill(['cancelled_at' => $cancelledAt, 'updated_at' => $cancelledAt])->save();
            $this->stampLatestHistory($order, $cancelledAt);

            return $cancelledAt;
        }

        // Every other target status passes through Paid first.
        $paidAt = $placedAt->copy()->addMinutes(fake()->numberBetween(5, 60));
        $channel = fake()->randomElement(self::PAYMENT_CHANNELS);
        $order->markAsPaid();
        $order->forceFill([
            'paid_at' => $paidAt,
            'payment_method' => 'midtrans',
            'payment_channel' => $channel,
            'updated_at' => $paidAt,
        ])->save();
        $this->stampLatestHistory($order, $paidAt);
        $this->recordSettlement($order, $paidAt, $channel);

        if ($target === OrderStatus::Paid) {
            return $paidAt;
        }

        $processingAt = $paidAt->copy()->addHours(fake()->numberBetween(1, 20));
        $order->markAsProcessing($admin);
        $order->forceFill(['updated_at' => $processingAt])->save();
        $this->stampLatestHistory($order, $processingAt);

        if ($target === OrderStatus::Processing) {
            return $processingAt;
        }

        $shippedAt = $processingAt->copy()->addHours(fake()->numberBetween(6, 48));
        $tracking = strtoupper(Str::random(12));
        $order->markAsShipped($admin, $courier, $tracking);
        $order->forceFill(['shipped_at' => $shippedAt, 'updated_at' => $shippedAt])->save();
        $this->stampLatestHistory($order, $shippedAt);

        if ($target === OrderStatus::Shipped) {
            return $shippedAt;
        }

        $completedAt = $shippedAt->copy()->addHours(fake()->numberBetween(24, 96));
        $order->markAsCompleted($admin);
        $order->forceFill(['completed_at' => $completedAt, 'updated_at' => $completedAt])->save();
        $this->stampLatestHistory($order, $completedAt);

        return $completedAt;
    }

    private function stampLatestHistory(Order $order, Carbon $at): void
    {
        $order->statusHistories()->latest()->first()?->update(['created_at' => $at]);
    }

    private function recordSettlement(Order $order, Carbon $paidAt, string $channel): void
    {
        $transaction = new PaymentTransaction([
            'order_id' => $order->id,
            'gateway' => 'midtrans',
            'gateway_transaction_id' => (string) Str::uuid(),
            'gateway_order_id' => "{$order->public_reference}-1",
            'status' => 'settlement',
            'payment_type' => $channel,
            'gross_amount' => $order->total,
            'snap_token' => Str::random(32),
            'raw_payload' => [
                'transaction_status' => 'settlement',
                'payment_type' => $channel,
                'gross_amount' => (string) $order->total,
                'order_id' => "{$order->public_reference}-1",
            ],
            'processed_at' => $paidAt,
        ]);

        // created_at/updated_at aren't fillable — set explicitly via
        // forceFill so this doesn't depend on db:seed's implicit
        // Model::unguard() wrapper to persist the backdated timestamp.
        $transaction->timestamps = false;
        $transaction->forceFill(['created_at' => $paidAt, 'updated_at' => $paidAt]);
        $transaction->save();
    }

    private function randomBusinessMoment(int $minDaysAgo, int $maxDaysAgo): Carbon
    {
        $daysAgo = fake()->numberBetween($minDaysAgo, $maxDaysAgo);

        return now()->subDays($daysAgo)
            ->setTime(fake()->numberBetween(8, 21), fake()->numberBetween(0, 59));
    }

    private function randomCourier(): array
    {
        $courier = fake()->randomElement(array_keys(self::COURIERS));
        $services = self::COURIERS[$courier];
        $serviceCode = fake()->randomElement(array_keys($services));

        return [$courier, $serviceCode, $services[$serviceCode]];
    }

    private function randomPhone(): string
    {
        return '08' . fake()->numerify(str_repeat('#', fake()->numberBetween(9, 11)));
    }
}
