<x-layouts.app :setting="$setting" title="Status Pesanan">
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-12">
        <h1 class="font-display font-semibold text-2xl text-stone-900 mb-2">Status Pesanan</h1>
        <p class="text-stone-500 text-sm mb-6">Halaman ini menampilkan status terkini pesanan Anda.</p>

        @include('orders._detail', ['order' => $order])

        @php $waUrl = \App\Services\WhatsAppLinkBuilder::forOrderStatus($order); @endphp
        @if($waUrl)
            <div class="mt-6 text-center">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 bg-whatsapp text-white font-semibold px-5 py-2.5 rounded-full hover:bg-whatsapp-dark transition-colors">
                    <x-icon name="chat-bubble" class="w-4 h-4" />
                    Tanya via WhatsApp
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
