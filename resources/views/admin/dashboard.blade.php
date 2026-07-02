<x-layouts.admin :setting="$setting" title="Dashboard">
    @if($newOrderCount > 0)
        <a href="{{ route('admin.pesanan.index', ['status' => 'paid']) }}"
           class="mb-4 flex items-center gap-4 bg-amber-50 border border-amber-200 rounded-xl p-5 hover:bg-amber-100 transition-colors">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-400 text-amber-950 shrink-0">
                <x-icon name="shopping-bag" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-amber-800">Pesanan baru menunggu diproses</p>
                <p class="text-2xl font-bold text-amber-900">{{ $newOrderCount }}</p>
            </div>
        </a>
    @endif

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-100 text-brand-700 shrink-0">
                <x-icon name="box" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-100 text-green-700 shrink-0">
                <x-icon name="check-circle" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-gray-500">Produk Aktif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $activeProducts }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-700 shrink-0">
                <x-icon name="tag" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-sm text-gray-500">Total Kategori</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalCategories }}</p>
            </div>
        </div>
    </div>
</x-layouts.admin>
