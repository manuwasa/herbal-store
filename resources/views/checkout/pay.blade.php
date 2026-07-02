<x-layouts.app :setting="$setting" title="Pembayaran">
    <div class="max-w-md mx-auto px-4 sm:px-6 py-16 text-center">
        <div class="bg-white rounded-2xl border border-stone-200 p-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-100 text-brand-700 mb-4">
                <x-icon name="shopping-bag" class="w-7 h-7" />
            </span>
            <h1 class="font-display font-semibold text-2xl text-stone-900">Selesaikan Pembayaran</h1>
            <p class="text-stone-500 mt-2 text-sm">Nomor Pesanan: <span class="font-mono">{{ $order->public_reference }}</span></p>
            <p class="text-2xl font-bold text-stone-900 mt-4">Rp{{ number_format($order->total, 0, ',', '.') }}</p>

            <button type="button" id="pay-button"
                    class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-brand-700 text-white font-semibold px-6 py-3 rounded-full hover:bg-brand-800 transition-colors">
                Bayar Sekarang
            </button>

            <p class="text-xs text-stone-400 mt-4">Batas waktu pembayaran: 2 jam sejak pesanan dibuat.</p>
        </div>
    </div>

    @push('scripts')
        <script src="https://app.{{ $isProduction ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
                data-client-key="{{ $snapClientKey }}"></script>
        <script>
            window.__snapToken = @json($snapToken);
            window.__confirmationUrl = @json(route('checkout.confirmation', $order));
            window.__statusUrl = @json(route('orders.show', $order));
        </script>
        <script src="{{ asset('js/checkout-pay.js') }}?v={{ filemtime(public_path('js/checkout-pay.js')) }}"></script>
    @endpush
</x-layouts.app>
