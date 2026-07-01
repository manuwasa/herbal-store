<x-layouts.app :setting="$setting" :title="$product->name" :description="$product->description ? \Illuminate\Support\Str::limit(strip_tags($product->description), 150) : null">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <a href="{{ route('catalog.index') }}" class="text-sm text-brand-700 hover:underline">&larr; Kembali ke Katalog</a>

        <div class="mt-4 grid md:grid-cols-2 gap-8">
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                @endif
            </div>

            <div>
                <p class="text-sm text-brand-700 font-medium">{{ $product->category->name }}</p>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                <p class="text-2xl font-bold text-gray-900 mt-4">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

                @if($product->stock <= 0)
                    <p class="mt-2 text-sm font-medium text-red-600">Stok habis</p>
                @endif

                @if($product->description)
                    <div class="mt-6 text-gray-600 whitespace-pre-line">{{ $product->description }}</div>
                @endif

                <div class="mt-8">
                    <x-action-buttons :product="$product" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
