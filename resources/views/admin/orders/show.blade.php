<x-layouts.admin :setting="\App\Models\Setting::current()" title="Detail Pesanan">
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.pesanan.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800">
            &larr; Kembali ke daftar pesanan
        </a>
        <a href="{{ route('admin.pesanan.invoice', $order) }}" target="_blank"
           class="icon-btn" title="Cetak Invoice">
            <x-icon name="receipt" class="w-4 h-4" />
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nomor Pesanan</p>
                        <p class="font-mono text-sm text-gray-700">{{ $order->public_reference }}</p>
                    </div>
                    <span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                </div>

                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-2 text-gray-700">{{ $item->product_name }} &times; {{ $item->quantity }}</td>
                                <td class="py-2 text-right font-medium text-gray-900">Rp{{ number_format($item->line_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-gray-200">
                        <tr><td class="py-1.5 text-gray-500">Subtotal</td><td class="py-1.5 text-right">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
                        <tr>
                            <td class="py-1.5 text-gray-500">Ongkir</td>
                            <td class="py-1.5 text-right">
                                @if($order->shipping_cost > 0)
                                    Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                                @else
                                    <span class="badge bg-amber-100 text-amber-700">Disepakati via WhatsApp</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="font-bold text-gray-900"><td class="py-1.5">Total</td><td class="py-1.5 text-right">Rp{{ number_format($order->total, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>

            {{-- Customer + shipping --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-sm">
                <h3 class="font-semibold text-gray-900 mb-3">Pengiriman</h3>
                <p class="text-gray-700 font-medium">{{ $order->customer_name }} &middot; {{ $order->customer_phone }}</p>
                @if($order->customer_email)<p class="text-gray-500">{{ $order->customer_email }}</p>@endif
                <p class="text-gray-600 mt-2 whitespace-pre-line">{{ $order->shipping_address }}</p>
                @if($order->shipping_area_label)<p class="text-gray-400 mt-1">Area: {{ $order->shipping_area_label }}</p>@endif
                @if($order->customer_note)<p class="text-gray-500 mt-2 italic">Catatan: {{ $order->customer_note }}</p>@endif
                <div class="mt-3 pt-3 border-t border-gray-100 text-gray-500">
                    <p>Cabang pemenuhan: <span class="font-medium text-gray-800">{{ $order->branch?->name ?? '—' }}</span></p>
                    @if($order->shipping_service_label)<p>Layanan: {{ $order->shipping_service_label }}</p>@endif
                    @if($order->tracking_number)<p>Resi: <span class="font-mono">{{ $order->courier_name }} {{ $order->tracking_number }}</span></p>@endif
                </div>
            </div>

            {{-- Payment transactions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-sm">
                <h3 class="font-semibold text-gray-900 mb-3">Transaksi Pembayaran</h3>
                @forelse($order->paymentTransactions as $tx)
                    <details class="border-b border-gray-100 py-2">
                        <summary class="cursor-pointer flex items-center justify-between">
                            <span class="text-gray-700 flex items-center gap-2">
                                {{ $tx->gateway }}
                                <span class="badge {{ $tx->statusBadgeClass() }}">{{ $tx->statusLabel() }}</span>
                                @if($tx->payment_type)<span class="text-gray-400">({{ $tx->payment_type }})</span>@endif
                            </span>
                            <span class="text-gray-400 text-xs">{{ $tx->created_at?->format('d M Y H:i') }}</span>
                        </summary>
                        <pre class="mt-2 bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto text-gray-600">{{ json_encode($tx->raw_payload, JSON_PRETTY_PRINT) }}</pre>
                    </details>
                @empty
                    <p class="text-gray-400">Belum ada transaksi.</p>
                @endforelse

                @if($order->refund_status)
                    <p class="mt-3 text-sm">
                        Status refund:
                        <span class="badge {{ $order->refund_status === 'refunded' ? 'bg-green-100 text-green-700' : ($order->refund_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ucfirst($order->refund_status) }}
                        </span>
                        @if($order->refund_status === 'failed')
                            <span class="text-red-600 text-xs">— perlu refund manual di dashboard Midtrans</span>
                        @endif
                    </p>
                @endif

                @if($order->shippingRateLookup)
                    <details class="mt-3">
                        <summary class="cursor-pointer text-gray-500 text-xs">Data ongkir (Biteship)</summary>
                        <pre class="mt-2 bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto text-gray-600">{{ json_encode($order->shippingRateLookup->raw_response, JSON_PRETTY_PRINT) }}</pre>
                    </details>
                @endif
            </div>
        </div>

        {{-- Sidebar: actions + timeline --}}
        <div class="space-y-5">
            {{-- Status actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Ubah Status</h3>
                @php $status = $order->status; @endphp

                @if($status === \App\Enums\OrderStatus::Paid)
                    <form method="POST" action="{{ route('admin.pesanan.update', $order) }}" class="mb-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="processing">
                        <button class="w-full bg-blue-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-blue-700">Proses Pesanan</button>
                    </form>
                @elseif($status === \App\Enums\OrderStatus::Processing)
                    <form method="POST" action="{{ route('admin.pesanan.update', $order) }}" class="mb-2 space-y-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="shipped">
                        <input type="text" name="courier_name" placeholder="Kurir (mis. JNE)" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <input type="text" name="tracking_number" placeholder="Nomor resi" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <button class="w-full bg-indigo-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-indigo-700">Tandai Dikirim</button>
                    </form>
                @elseif($status === \App\Enums\OrderStatus::Shipped)
                    <form method="POST" action="{{ route('admin.pesanan.update', $order) }}" class="mb-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="completed">
                        <button class="w-full bg-emerald-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-emerald-700">Tandai Selesai</button>
                    </form>
                @endif

                @if(in_array($status, [\App\Enums\OrderStatus::PendingPayment, \App\Enums\OrderStatus::Paid, \App\Enums\OrderStatus::Processing], true))
                    <form method="POST" action="{{ route('admin.pesanan.update', $order) }}"
                          onsubmit="return confirm('Batalkan pesanan ini? Jika sudah dibayar, sistem akan mencoba refund otomatis.');">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="cancelled">
                        <input type="text" name="note" placeholder="Alasan pembatalan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2">
                        <button class="w-full bg-red-600 text-white text-sm font-semibold py-2 rounded-lg hover:bg-red-700">Batalkan Pesanan</button>
                    </form>
                @endif

                @if($status === \App\Enums\OrderStatus::Completed || $status === \App\Enums\OrderStatus::Cancelled || $status === \App\Enums\OrderStatus::Expired)
                    <p class="text-sm text-gray-400">Pesanan sudah final.</p>
                @endif
            </div>

            {{-- Branch reassignment (before shipped) --}}
            @if(auth()->user()->isOwner() && in_array($status, [\App\Enums\OrderStatus::PendingPayment, \App\Enums\OrderStatus::Paid, \App\Enums\OrderStatus::Processing], true))
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">Pindah Cabang</h3>
                    <form method="POST" action="{{ route('admin.pesanan.reassign-branch', $order) }}" class="space-y-2">
                        @csrf @method('PUT')
                        <select name="branch_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($order->branch_id == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <button class="w-full bg-gray-800 text-white text-sm font-semibold py-2 rounded-lg hover:bg-gray-900">Pindahkan</button>
                    </form>
                </div>
            @endif

            {{-- Status history timeline --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Riwayat Status</h3>
                <ol class="space-y-3 text-sm">
                    @forelse($order->statusHistories as $history)
                        <li class="flex gap-3">
                            <span class="mt-1 w-2 h-2 rounded-full bg-brand-500 shrink-0"></span>
                            <div>
                                <p class="text-gray-700">
                                    {{ $history->from_status ? \App\Enums\OrderStatus::from($history->from_status)->label() . ' → ' : '' }}
                                    <span class="font-medium">{{ \App\Enums\OrderStatus::from($history->to_status)->label() }}</span>
                                </p>
                                @if($history->note)<p class="text-gray-500 text-xs">{{ $history->note }}</p>@endif
                                <p class="text-gray-400 text-xs">
                                    {{ $history->created_at?->format('d M Y H:i') }} —
                                    {{ $history->changedBy?->name ?? 'otomatis (sistem)' }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-400">Belum ada riwayat.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</x-layouts.admin>
