<x-layouts.app :setting="$setting">
    <section class="bg-brand-50 border-b border-brand-100">
        <div class="max-w-6xl mx-auto px-4 py-16 flex flex-col md:flex-row items-center gap-8">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $setting->banner_heading }}</h1>
                @if($setting->banner_subheading)
                    <p class="mt-4 text-lg text-gray-600">{{ $setting->banner_subheading }}</p>
                @endif
                <a href="{{ route('catalog.index') }}" class="inline-block mt-6 bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-brand-700">
                    Lihat Katalog
                </a>
            </div>
            @if($setting->banner_image_path)
                <div class="flex-1">
                    <img src="{{ asset('storage/' . $setting->banner_image_path) }}" alt="{{ $setting->site_name }}" class="w-full rounded-xl">
                </div>
            @endif
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Produk Terbaru</h2>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-brand-700 hover:underline">Lihat semua</a>
        </div>

        @if($featuredProducts->isEmpty())
            <p class="text-gray-500">Belum ada produk.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </section>

    @if($categories->isNotEmpty())
        <section class="max-w-6xl mx-auto px-4 py-12 border-t border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Kategori</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($categories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                       class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm font-medium text-gray-700 hover:border-brand-400 hover:text-brand-700">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
