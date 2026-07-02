<x-layouts.app :setting="$setting" title="Keranjang">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
        <h1 class="font-display font-semibold text-3xl text-stone-900 mb-8">Keranjang Belanja</h1>

        @if($errors->any())
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <x-icon name="alert" class="w-5 h-5 shrink-0" />
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        @if($cartItems->isEmpty())
            <div class="text-center py-20 text-stone-400">
                <x-icon name="cart" class="w-12 h-12 mx-auto mb-3" />
                <p>Keranjang Anda masih kosong.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block mt-4 bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-full hover:bg-brand-800 transition-colors">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 divide-y divide-stone-100 px-5">
                @foreach($cartItems as $item)
                    <x-cart-item :item="$item" :editable="true" />
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-2xl border border-stone-200 p-5">
                <div class="flex items-center justify-between text-stone-600">
                    <span>Subtotal</span>
                    <span class="font-semibold text-stone-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-stone-500 text-sm mt-2">
                    <span>Ongkos kirim</span>
                    <span>Dihitung saat checkout</span>
                </div>
                <a href="{{ route('checkout.index') }}"
                   class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-brand-700 text-white font-semibold px-6 py-3 rounded-full hover:bg-brand-800 transition-colors">
                    Lanjut ke Checkout
                    <x-icon name="arrow-top-right" class="w-4 h-4" />
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
