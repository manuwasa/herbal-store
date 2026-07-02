<x-layouts.app :setting="$setting" :title="$product->name" :description="$product->description ? \Illuminate\Support\Str::limit(strip_tags($product->description), 150) : null">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <nav class="flex items-center gap-1.5 text-sm text-stone-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand-700">Beranda</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-brand-700">Katalog</a>
            <span>/</span>
            <span class="text-stone-700 truncate">{{ $product->name }}</span>
        </nav>

        <div class="grid md:grid-cols-2 gap-10">
            <div class="relative aspect-square bg-stone-100 rounded-2xl overflow-hidden">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @elseif($setting->product_placeholder_image_path)
                    <img src="{{ asset('storage/' . $setting->product_placeholder_image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-stone-300">
                        <x-icon name="leaf" class="w-16 h-16" />
                    </div>
                @endif
            </div>

            <div>
                <p class="text-xs font-medium text-brand-700 uppercase tracking-wide">{{ $product->category->name }}</p>
                <h1 class="font-display font-semibold text-3xl text-stone-900 mt-2 text-balance">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-stone-900 mt-4">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

                @if($product->totalStock() <= 0)
                    <span class="badge bg-red-100 text-red-700 mt-3">Stok Habis</span>
                @endif

                @if($product->description)
                    <div class="mt-6 text-stone-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</div>
                @endif

                <div class="mt-8 pt-8 border-t border-stone-200">
                    <p class="text-sm font-medium text-stone-500 mb-3">Pesan produk ini via:</p>
                    <x-action-buttons :product="$product" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
