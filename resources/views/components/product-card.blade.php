@props(['product'])

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col">
    <a href="{{ route('catalog.show', $product) }}" class="block aspect-square bg-gray-100">
        @if($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Tidak ada gambar</div>
        @endif
    </a>

    <div class="p-4 flex flex-col gap-2 flex-1">
        <p class="text-xs text-brand-700 font-medium">{{ $product->category->name }}</p>
        <a href="{{ route('catalog.show', $product) }}" class="font-semibold text-gray-900 hover:text-brand-700">
            {{ $product->name }}
        </a>
        <p class="text-lg font-bold text-gray-900">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

        <div class="mt-auto pt-2">
            <x-action-buttons :product="$product" :compact="true" />
        </div>
    </div>
</div>
