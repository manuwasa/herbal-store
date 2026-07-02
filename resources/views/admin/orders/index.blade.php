<x-layouts.admin :setting="\App\Models\Setting::current()" title="Pesanan">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-100 text-brand-700">
                    <x-icon name="shopping-bag" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Daftar Pesanan</h2>
            </div>
            <div id="orders-table-search-slot" class="flex-1 sm:flex-initial"></div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap items-end gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/50">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isOwner())
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cabang</label>
                    <select name="branch_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="bg-gray-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg hover:bg-gray-900">Filter</button>
            @if(request()->hasAny(['status', 'branch_id']))
                <a href="{{ route('admin.pesanan.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Reset</a>
            @endif
        </form>

        <div class="p-5 overflow-x-auto">
            <table id="orders-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Telepon</th>
                        <th>Cabang</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="whitespace-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="font-medium text-gray-900">{{ $order->customer_name }}</td>
                            <td>{{ $order->customer_phone }}</td>
                            <td>{{ $order->branch?->name ?? '—' }}</td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.pesanan.show', $order) }}" class="icon-btn" title="Detail">
                                    <x-icon name="search" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
