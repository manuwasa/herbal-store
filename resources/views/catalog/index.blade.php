<x-layouts.app :setting="$setting" title="Katalog">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1.5">Semua Produk</p>
        <h1 class="font-display font-semibold text-3xl text-stone-900 mb-8">Katalog Produk</h1>

        <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-10">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 text-stone-400 absolute left-4 top-1/2 -translate-y-1/2" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk..."
                       class="w-full border border-stone-300 rounded-full pl-11 pr-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            </div>

            <select name="category" class="border border-stone-300 rounded-full px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-brand-700 text-white font-semibold px-7 py-2.5 rounded-full hover:bg-brand-800 transition-colors">
                Filter
            </button>
        </form>

        @if($products->isEmpty())
            <div class="text-center py-20 text-stone-400">
                <x-icon name="search" class="w-10 h-10 mx-auto mb-3" />
                <p>Tidak ada produk yang ditemukan.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
