@props(['item', 'editable' => false])

@php
    $product = $item->product;
    $placeholderPath = \App\Models\Setting::current()->product_placeholder_image_path;
@endphp

<div class="flex items-center gap-4 py-4">
    <div class="w-16 h-16 shrink-0 rounded-lg bg-stone-100 overflow-hidden flex items-center justify-center">
        @if($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @elseif($placeholderPath)
            <img src="{{ asset('storage/' . $placeholderPath) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <x-icon name="leaf" class="w-6 h-6 text-stone-300" />
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <p class="font-medium text-stone-900 leading-snug line-clamp-2">{{ $product->name }}</p>
        <p class="text-sm text-stone-500 mt-0.5">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

        @if($editable)
            <div class="flex items-center gap-3 mt-2">
                <form method="POST" action="{{ route('cart.update', $product) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <x-quantity-stepper name="quantity" :value="$item->quantity" :max="$product->totalStock()" />
                    <button type="submit" class="text-xs text-brand-700 hover:underline">Perbarui</button>
                </form>

                <form method="POST" action="{{ route('cart.destroy', $product) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        @else
            <p class="text-sm text-stone-500 mt-0.5">Jumlah: {{ $item->quantity }}</p>
        @endif
    </div>

    <div class="text-right shrink-0">
        <p class="font-semibold text-stone-900">Rp{{ number_format($item->lineTotal(), 0, ',', '.') }}</p>
    </div>
</div>
