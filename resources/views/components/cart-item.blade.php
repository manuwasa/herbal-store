@props(['item', 'editable' => false])

@php
    $product = $item->product;
    $placeholderPath = \App\Models\Setting::current()->product_placeholder_image_path;
@endphp

<div class="flex items-center gap-4 py-4" data-cart-row data-product-id="{{ $product->id }}" data-unit-price="{{ $product->price }}">
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
            <div class="flex items-center gap-2 mt-2.5">
                <form method="POST" action="{{ route('cart.update', $product) }}" data-cart-update-form>
                    @csrf
                    @method('PATCH')
                    <x-quantity-stepper name="quantity" :value="$item->quantity" :max="$product->totalStock()" />
                </form>

                <form method="POST" action="{{ route('cart.destroy', $product) }}" data-cart-remove-form>
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-full transition-colors">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                        Hapus
                    </button>
                </form>
            </div>
        @else
            <p class="text-sm text-stone-500 mt-0.5">Jumlah: {{ $item->quantity }}</p>
        @endif
    </div>

    <div class="text-right shrink-0">
        <p class="font-semibold text-stone-900" data-line-total>Rp{{ number_format($item->lineTotal(), 0, ',', '.') }}</p>
    </div>
</div>
