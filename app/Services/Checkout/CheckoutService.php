<?php

namespace App\Services\Checkout;

use App\Exceptions\CartValidationException;
use App\Models\Branch;
use App\Models\Order;
use App\Models\ShippingRateLookup;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(private CartService $cart)
    {
    }

    /**
     * Build an Order + OrderItems from the current cart.
     *
     * $buyerData is ONLY the validated guest fields plus shipping-selection
     * identifiers (never a raw price). Money/branch/status are computed here and
     * merged as explicit keys — nothing from the request can reach them.
     *
     * @param  array<string, mixed>  $buyerData
     */
    public function createOrder(array $buyerData, ?string $sessionId): Order
    {
        $lines = $this->cart->items();

        if ($lines->isEmpty()) {
            throw new CartValidationException('Keranjang Anda kosong.');
        }

        return DB::transaction(function () use ($buyerData, $sessionId, $lines) {
            // (1) Resolve which branch fulfils this order + the trusted shipping price,
            //     from the ShippingRateLookup recorded during the AJAX rate check.
            $lookup = $this->resolveLookup($buyerData, $sessionId);
            $branch = $this->resolveBranch($lookup);

            if (! $branch) {
                throw new CartValidationException('Tidak ada cabang yang dapat memproses pesanan Anda saat ini.');
            }

            // (2) Re-validate live stock against that specific branch (may have moved
            //     since the quote).
            foreach ($lines as $line) {
                if (! $line->product->is_active) {
                    throw new CartValidationException("Produk \"{$line->product->name}\" sudah tidak tersedia.");
                }
                if ($line->product->stockAt($branch) < $line->quantity) {
                    throw new CartValidationException(
                        "Maaf, stok \"{$line->product->name}\" tidak cukup di cabang terdekat Anda."
                    );
                }
            }

            // (3) Server-computed money — shipping price re-derived from the lookup row,
            //     never the client-submitted value.
            $subtotal = $this->cart->subtotal();
            $shippingCost = $lookup?->selected_price !== null ? (string) $lookup->selected_price : '0';
            $total = bcadd($subtotal, $shippingCost, 2);

            // (4) Create the order — status defaults to pending_payment at the DB level.
            $order = Order::create([
                'customer_name' => $buyerData['customer_name'],
                'customer_phone' => $buyerData['customer_phone'],
                'customer_email' => $buyerData['customer_email'] ?? null,
                'shipping_address' => $buyerData['shipping_address'],
                'customer_note' => $buyerData['customer_note'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_area_id' => $lookup?->destination_area_id,
                'shipping_area_label' => $buyerData['shipping_area_label'] ?? null,
                'shipping_courier' => $lookup?->selected_courier,
                'shipping_service' => $lookup?->selected_service,
                'shipping_service_label' => $buyerData['shipping_service_label'] ?? null,
                'shipping_weight_grams' => $lookup?->weight_grams,
                'privacy_consent_at' => now(),
                'expires_at' => now()->addHours(2),
            ]);

            // branch_id is not fillable — set it on the controlled path.
            $order->forceFill(['branch_id' => $branch->id])->save();

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_name' => $line->product->name,
                    'product_slug' => $line->product->slug,
                    'unit_price' => $line->product->price,
                    'quantity' => $line->quantity,
                    'line_total' => $line->lineTotal(),
                ]);
            }

            if ($lookup) {
                $lookup->update(['order_id' => $order->id]);
            }

            $this->cart->clear();

            return $order;
        });
    }

    private function resolveLookup(array $buyerData, ?string $sessionId): ?ShippingRateLookup
    {
        $areaId = $buyerData['shipping_area_id'] ?? null;

        if (blank($areaId) || blank($sessionId)) {
            return null;
        }

        return ShippingRateLookup::query()
            ->whereNull('order_id')
            ->where('session_id', $sessionId)
            ->where('destination_area_id', $areaId)
            ->when(
                filled($buyerData['shipping_courier'] ?? null),
                fn ($q) => $q->where('selected_courier', $buyerData['shipping_courier'])
                    ->where('selected_service', $buyerData['shipping_service'] ?? null)
            )
            ->latest('id')
            ->first();
    }

    private function resolveBranch(?ShippingRateLookup $lookup): ?Branch
    {
        if ($lookup?->branch_id) {
            return Branch::find($lookup->branch_id);
        }

        // No successful rate lookup (Biteship down / unconfigured): fall back to the
        // default branch, same soft-degradation as an outage.
        return Branch::default();
    }
}
