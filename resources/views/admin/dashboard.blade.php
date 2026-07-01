<x-layouts.admin :setting="$setting" title="Dashboard">
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Produk</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Produk Aktif</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeProducts }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Kategori</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCategories }}</p>
        </div>
    </div>
</x-layouts.admin>
