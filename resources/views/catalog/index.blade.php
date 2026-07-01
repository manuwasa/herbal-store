<x-layouts.app :setting="$setting" title="Katalog">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Katalog Produk</h1>

        <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-8">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk..."
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm">

            <select name="category" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-brand-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-brand-700">
                Filter
            </button>
        </form>

        @if($products->isEmpty())
            <p class="text-gray-500">Tidak ada produk yang ditemukan.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
