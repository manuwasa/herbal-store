{{-- Shared read-only order summary, used by the confirmation + status pages. --}}
<div class="mt-6 bg-white rounded-2xl border border-stone-200 p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-xs text-stone-400 uppercase tracking-wide">Nomor Pesanan</p>
            <p class="font-mono text-sm text-stone-700">{{ $order->public_reference }}</p>
        </div>
        <span
            class="badge {{ $order->status->badgeClass() }} max-w-24 text-center whitespace-normal">{{ $order->status->label() }}</span>
    </div>

    <div class="divide-y divide-stone-100">
        @foreach ($order->items as $item)
            <div class="flex items-center justify-between py-2 text-sm">
                <span class="text-stone-600">{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                <span class="font-medium text-stone-900">Rp{{ number_format($item->line_total, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-4 pt-4 border-t border-stone-200 space-y-1.5 text-sm">
        <div class="flex items-center justify-between text-stone-600">
            <span>Subtotal</span>
            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between text-stone-600">
            <span>Ongkos kirim @if ($order->shipping_service_label)
                    <span class="text-stone-400">({{ $order->shipping_service_label }})</span>
                @endif
            </span>
            <span>{{ $order->shipping_cost > 0 ? 'Rp' . number_format($order->shipping_cost, 0, ',', '.') : 'Disepakati via WhatsApp' }}</span>
        </div>
        <div class="flex items-center justify-between font-bold text-stone-900 pt-1.5 border-t border-stone-100">
            <span>Total</span>
            <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    @if ($order->tracking_number)
        <div class="mt-4 pt-4 border-t border-stone-200 text-sm">
            <p class="text-stone-500">Kurir: <span class="text-stone-800 font-medium">{{ $order->courier_name }}</span>
            </p>
            <p class="text-stone-500">No. Resi: <span
                    class="text-stone-800 font-mono">{{ $order->tracking_number }}</span></p>
        </div>
    @endif

    <div class="mt-4 pt-4 border-t border-stone-200 text-sm text-stone-500">
        <p class="font-medium text-stone-700 mb-1">Dikirim ke:</p>
        <p>{{ $order->customer_name }} &middot; {{ $order->customer_phone }}</p>
        <p class="whitespace-pre-line">{{ $order->shipping_address }}</p>
    </div>
</div>
