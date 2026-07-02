@props(['product'])

@php $placeholderPath = \App\Models\Setting::current()->product_placeholder_image_path; @endphp

<div {{ $attributes->merge(['class' => 'group bg-white rounded-2xl border border-stone-200 overflow-hidden flex flex-col transition-all duration-200 hover:shadow-lg hover:shadow-stone-900/5 hover:-translate-y-0.5 hover:border-stone-300']) }}>
    <a href="{{ route('catalog.show', $product) }}" class="relative block aspect-square bg-stone-100 overflow-hidden">
        @if($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        @elseif($placeholderPath)
            <img src="{{ asset('storage/' . $placeholderPath) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-300">
                <x-icon name="leaf" class="w-10 h-10" />
            </div>
        @endif

        @if($product->totalStock() <= 0)
            <span class="absolute top-2.5 left-2.5 badge bg-stone-900/80 text-white backdrop-blur-sm">Stok Habis</span>
        @endif
    </a>

    <div class="p-4 flex flex-col gap-1.5 flex-1">
        <p class="text-xs font-medium text-brand-700 uppercase tracking-wide">{{ $product->category->name }}</p>
        <a href="{{ route('catalog.show', $product) }}" class="font-semibold text-stone-900 hover:text-brand-700 leading-snug line-clamp-2">
            {{ $product->name }}
        </a>
        <p class="text-lg font-bold text-stone-900 mt-0.5">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

        <div class="mt-auto pt-3">
            <x-action-buttons :product="$product" :compact="true" />
        </div>
    </div>
</div>
