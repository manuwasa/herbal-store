<x-layouts.admin :setting="\App\Models\Setting::current()" title="Laporan Penjualan">
    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
            <input type="date" name="dari" value="{{ $from }}" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
            <input type="date" name="sampai" value="{{ $to }}" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
        </div>
        @if(auth()->user()->isOwner())
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                <select name="branch_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button type="submit" class="bg-gray-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg hover:bg-gray-900">Terapkan</button>
    </form>

    {{-- Summary cards --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-100 text-green-700 shrink-0">
                <x-icon name="chart" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-100 text-brand-700 shrink-0">
                <x-icon name="shopping-bag" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-gray-500">Jumlah Pesanan (Dibayar)</p>
                <p class="text-2xl font-bold text-gray-900">{{ $orderCount }}</p>
            </div>
        </div>
    </div>

    {{-- Revenue by day --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-5">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Pendapatan Harian</h2>
            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'harian'])) }}"
               class="inline-flex items-center gap-1.5 text-sm text-brand-700 hover:underline">
                <x-icon name="download" class="w-4 h-4" /> Ekspor CSV
            </a>
        </div>
        <div class="p-5 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b border-gray-200">
                    <th class="py-2">Tanggal</th><th class="py-2 text-right">Pesanan</th><th class="py-2 text-right">Pendapatan</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($revenueByDay as $row)
                        <tr>
                            <td class="py-2">{{ $row['date'] }}</td>
                            <td class="py-2 text-right">{{ $row['count'] }}</td>
                            <td class="py-2 text-right font-medium">Rp{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada data pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top products --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Produk Terlaris</h2>
            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'produk'])) }}"
               class="inline-flex items-center gap-1.5 text-sm text-brand-700 hover:underline">
                <x-icon name="download" class="w-4 h-4" /> Ekspor CSV
            </a>
        </div>
        <div class="p-5 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b border-gray-200">
                    <th class="py-2">Produk</th><th class="py-2 text-right">Terjual</th><th class="py-2 text-right">Pendapatan</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topProducts as $row)
                        <tr>
                            <td class="py-2 font-medium text-gray-900">{{ $row->product_name }}</td>
                            <td class="py-2 text-right">{{ $row->qty }}</td>
                            <td class="py-2 text-right font-medium">Rp{{ number_format($row->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">Tidak ada data pada rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
