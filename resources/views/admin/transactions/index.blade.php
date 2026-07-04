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

        <details class="border-b border-gray-200 px-5 py-3 text-sm">
            <summary class="cursor-pointer font-medium text-gray-600">Keterangan status</summary>
            <dl class="mt-3 grid sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2">
                @foreach([
                    'settlement' => 'Pembayaran berhasil diterima — pesanan sudah lunas.',
                    'pending' => 'Pembeli belum menyelesaikan pembayaran (menunggu transfer, scan QRIS, dll).',
                    'capture' => 'Otorisasi kartu kredit diterima, masih menunggu verifikasi tambahan dari gateway.',
                    'deny' => 'Pembayaran ditolak oleh bank atau sistem deteksi fraud.',
                    'cancel' => 'Transaksi dibatalkan sebelum pembayaran selesai.',
                    'expire' => 'Batas waktu pembayaran habis sebelum pembeli membayar.',
                    'refund' => 'Dana sudah dikembalikan penuh ke pembeli.',
                    'partial_refund' => 'Sebagian dana sudah dikembalikan ke pembeli.',
                    'refund_failed' => 'Refund otomatis gagal — perlu ditindaklanjuti manual di dashboard Midtrans.',
                ] as $statusKey => $explanation)
                    @php($sample = new \App\Models\PaymentTransaction(['status' => $statusKey]))
                    <div class="flex items-start gap-2">
                        <span class="badge {{ $sample->statusBadgeClass() }} shrink-0 mt-0.5">{{ $sample->statusLabel() }}</span>
                        <span class="text-gray-500">{{ $explanation }}</span>
                    </div>
                @endforeach
            </dl>
        </details>

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
                            <td><span class="badge {{ $tx->statusBadgeClass() }}">{{ $tx->statusLabel() }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
