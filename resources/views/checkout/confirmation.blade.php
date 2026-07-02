<x-layouts.app :setting="$setting" title="Pesanan Diterima">
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-2xl border border-stone-200 p-8 text-center">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 text-green-700 mb-4">
                <x-icon name="check-circle" class="w-8 h-8" />
            </span>
            <h1 class="font-display font-semibold text-2xl text-stone-900">Terima Kasih!</h1>
            <p class="text-stone-500 mt-2">Pesanan Anda sudah kami terima.</p>

            <div class="mt-4 inline-flex items-center gap-2">
                <span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
            </div>
        </div>

        @include('orders._detail', ['order' => $order])

        @php $waUrl = \App\Services\WhatsAppLinkBuilder::forOrderStatus($order); @endphp
        <div class="mt-6 bg-white rounded-2xl border border-stone-200 p-5 text-center">
            <p class="text-sm text-stone-600 mb-3">Simpan link status pesanan Anda:</p>
            @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 bg-whatsapp text-white font-semibold px-5 py-2.5 rounded-full hover:bg-whatsapp-dark transition-colors">
                    <x-icon name="chat-bubble" class="w-4 h-4" />
                    Kirim Link ke WhatsApp Saya
                </a>
            @endif
            <p class="mt-3 text-xs text-stone-500 break-all">
                atau simpan link ini:
                <a href="{{ route('orders.show', $order) }}" class="text-brand-700 hover:underline">{{ route('orders.show', $order) }}</a>
            </p>
        </div>
    </div>
</x-layouts.app>
