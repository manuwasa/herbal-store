<x-layouts.admin :setting="\App\Models\Setting::current()" title="Riwayat Transaksi">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-purple-100 text-purple-700">
                    <x-icon name="receipt" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Riwayat Transaksi Pembayaran</h2>
            </div>
            <div id="transactions-table-search-slot" class="flex-1 sm:flex-initial"></div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="transactions-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Cabang</th>
                        <th>Gateway</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        <tr>
                            <td class="whitespace-nowrap">{{ $tx->created_at?->format('d M Y H:i') }}</td>
                            <td class="font-mono text-xs">
                                <a href="{{ route('admin.pesanan.show', $tx->order_id) }}" class="text-brand-700 hover:underline">
                                    {{ \Illuminate\Support\Str::limit($tx->order?->public_reference, 8, '') }}
                                </a>
                            </td>
                            <td>{{ $tx->order?->customer_name ?? '—' }}</td>
                            <td>{{ $tx->order?->branch?->name ?? '—' }}</td>
                            <td>{{ $tx->gateway }}</td>
                            <td>{{ $tx->payment_type ?? '—' }}</td>
                            <td>Rp{{ number_format($tx->gross_amount, 0, ',', '.') }}</td>
                            <td>{{ $tx->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
