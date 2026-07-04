{{-- Shared invoice content, included by both the guest-facing and admin-facing
     invoice wrapper views (resources/views/orders/invoice.blade.php and
     resources/views/admin/orders/invoice.blade.php). Print-oriented layout —
     no card chrome, no site navbar, meant to look right on paper. --}}
<div class="bg-white rounded-2xl border border-stone-200 p-8 print:border-0 print:rounded-none print:p-0">
    {{-- Letterhead --}}
    <div class="flex items-start justify-between gap-4 pb-6 border-b border-stone-200">
        <div class="flex items-center gap-3">
            @if($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="h-10 w-auto">
            @endif
            <div>
                <p class="font-display font-semibold text-lg text-stone-900">{{ $setting->site_name }}</p>
                @if($setting->contact_address)<p class="text-xs text-stone-500 whitespace-pre-line">{{ $setting->contact_address }}</p>@endif
                <p class="text-xs text-stone-500">
                    @if($setting->contact_phone){{ $setting->contact_phone }}@endif
                    @if($setting->contact_phone && $setting->contact_email) &middot; @endif
                    @if($setting->contact_email){{ $setting->contact_email }}@endif
                </p>
            </div>
        </div>
        <div class="text-right shrink-0">
            <p class="font-display font-bold text-xl text-stone-900 tracking-wide">INVOICE</p>
            <p class="font-mono text-sm text-stone-600 mt-1">{{ $order->invoice_number ?? '—' }}</p>
            <p class="text-xs text-stone-400 mt-1">{{ $order->created_at?->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Status + bill-to --}}
    <div class="flex flex-wrap items-start justify-between gap-4 py-6 border-b border-stone-200">
        <div class="text-sm">
            <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Ditagihkan kepada</p>
            <p class="font-medium text-stone-900">{{ $order->customer_name }}</p>
            <p class="text-stone-600">{{ $order->customer_phone }}</p>
            @if($order->customer_email)<p class="text-stone-600">{{ $order->customer_email }}</p>@endif
            <p class="text-stone-600 whitespace-pre-line mt-1">{{ $order->shipping_address }}</p>
        </div>
        <div class="text-sm text-right">
            <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Status</p>
            <span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
            @if($order->branch)<p class="text-stone-500 mt-2">Cabang: {{ $order->branch->name }}</p>@endif
            <p class="text-stone-500 mt-1">
                Pembayaran:
                @if($order->latestPaymentTransaction)
                    <span class="badge {{ $order->latestPaymentTransaction->statusBadgeClass() }}">{{ $order->latestPaymentTransaction->statusLabel() }}</span>
                    @if($order->payment_channel)<span class="text-stone-400">({{ $order->payment_channel }})</span>@endif
                @else
                    <span class="badge bg-amber-100 text-amber-700">Belum Dibayar</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Line items --}}
    <table class="w-full text-sm mt-6">
        <thead>
            <tr class="text-left text-xs text-stone-400 uppercase tracking-wide border-b border-stone-200">
                <th class="pb-2 font-medium">Produk</th>
                <th class="pb-2 font-medium text-center">Qty</th>
                <th class="pb-2 font-medium text-right">Harga</th>
                <th class="pb-2 font-medium text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($order->items as $item)
                <tr>
                    <td class="py-2 text-stone-700">{{ $item->product_name }}</td>
                    <td class="py-2 text-center text-stone-600">{{ $item->quantity }}</td>
                    <td class="py-2 text-right text-stone-600">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="py-2 text-right font-medium text-stone-900">Rp{{ number_format($item->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="mt-4 pt-4 border-t border-stone-200 space-y-1.5 text-sm ml-auto max-w-xs">
        <div class="flex items-center justify-between text-stone-600">
            <span>Subtotal</span>
            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between text-stone-600">
            <span>Ongkos kirim @if($order->shipping_service_label)<span class="text-stone-400">({{ $order->shipping_service_label }})</span>@endif</span>
            <span>{{ $order->shipping_cost > 0 ? 'Rp' . number_format($order->shipping_cost, 0, ',', '.') : 'Disepakati via WhatsApp' }}</span>
        </div>
        <div class="flex items-center justify-between font-bold text-stone-900 pt-1.5 border-t border-stone-200 text-base">
            <span>Total</span>
            <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($order->tracking_number)
        <div class="mt-6 pt-4 border-t border-stone-200 text-sm text-stone-500">
            <p>Kurir: <span class="text-stone-800 font-medium">{{ $order->courier_name }}</span></p>
            <p>No. Resi: <span class="text-stone-800 font-mono">{{ $order->tracking_number }}</span></p>
        </div>
    @endif

    <div class="mt-8 pt-4 border-t border-stone-200 text-center text-xs text-stone-400">
        Terima kasih telah berbelanja di {{ $setting->site_name }}.
    </div>
</div>
