<x-layouts.admin :setting="\App\Models\Setting::current()" title="Transfer Stok">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-teal-100 text-teal-700">
                    <x-icon name="truck" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Riwayat Transfer Stok</h2>
            </div>
            <div class="flex items-center gap-2">
                <div id="stock-transfers-table-search-slot" class="flex-1 sm:flex-initial"></div>
                <a href="{{ route('admin.transfer-stok.create') }}"
                   class="shrink-0 inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-700 whitespace-nowrap">
                    <x-icon name="plus" class="w-4 h-4" />
                    Catat Transfer
                </a>
            </div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="stock-transfers-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Jumlah</th>
                        <th>Oleh</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr>
                            <td class="whitespace-nowrap">{{ $transfer->created_at?->format('d M Y H:i') }}</td>
                            <td class="font-medium text-gray-900">{{ $transfer->product?->name ?? '—' }}</td>
                            <td>{{ $transfer->fromBranch?->name ?? 'Stok Baru' }}</td>
                            <td>{{ $transfer->toBranch?->name ?? '—' }}</td>
                            <td>{{ $transfer->quantity }}</td>
                            <td>{{ $transfer->createdBy?->name ?? '—' }}</td>
                            <td class="text-gray-500">{{ $transfer->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
