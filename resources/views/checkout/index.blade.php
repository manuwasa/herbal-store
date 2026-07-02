<x-layouts.app :setting="$setting" title="Checkout">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
        <h1 class="font-display font-semibold text-3xl text-stone-900 mb-8">Checkout</h1>

        @if($errors->any())
            <div class="mb-6 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <x-icon name="alert" class="w-5 h-5 shrink-0" />
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form" class="grid md:grid-cols-2 gap-8">
            @csrf

            {{-- Buyer + shipping details --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-stone-200 p-5 space-y-4">
                    <h2 class="font-semibold text-stone-900">Data Pemesan</h2>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Nomor WhatsApp / Telepon <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required
                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Email (opsional, untuk kirim struk)</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-stone-200 p-5 space-y-4">
                    <h2 class="font-semibold text-stone-900">Alamat Pengiriman</h2>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Kota / Kecamatan Tujuan <span class="text-red-500">*</span></label>
                        <x-area-search-field name="shipping_area" />
                        <p class="text-xs text-stone-400 mt-1">Ketik minimal 3 huruf, lalu pilih dari daftar.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Alamat Lengkap (jalan, no. rumah, RT/RW, kode pos) <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" rows="3" required
                                  class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('shipping_address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Catatan untuk penjual (opsional)</label>
                        <textarea name="customer_note" rows="2"
                                  class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('customer_note') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Order summary + shipping options --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-stone-200 p-5">
                    <h2 class="font-semibold text-stone-900 mb-4">Ringkasan Pesanan</h2>

                    <div class="divide-y divide-stone-100">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between py-2 text-sm">
                                <span class="text-stone-600">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                                <span class="font-medium text-stone-900">Rp{{ number_format($item->lineTotal(), 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-stone-200 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-stone-600">Subtotal</span>
                            <span class="font-medium text-stone-900" data-subtotal="{{ $subtotal }}">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        {{-- Shipping options are injected here by checkout-shipping.js after an area is picked. --}}
                        <div id="shipping-options" class="text-sm text-stone-500">
                            <p>Pilih area tujuan dulu untuk menghitung ongkir.</p>
                        </div>

                        <div class="flex items-center justify-between text-base font-bold text-stone-900 pt-2 border-t border-stone-200">
                            <span>Total</span>
                            <span id="grand-total">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Populated by JS from the chosen shipping option. Price here is display-only;
                         the server re-derives the authoritative price from the recorded lookup. --}}
                    <input type="hidden" name="shipping_courier" id="shipping_courier" value="{{ old('shipping_courier') }}">
                    <input type="hidden" name="shipping_service" id="shipping_service" value="{{ old('shipping_service') }}">
                    <input type="hidden" name="shipping_service_label" id="shipping_service_label" value="{{ old('shipping_service_label') }}">
                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">

                    @if($setting->recaptcha_enabled && $setting->recaptcha_site_key)
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                    @endif

                    <label class="flex items-start gap-2 text-xs text-stone-600 mt-5">
                        <input type="checkbox" name="privacy_consent" value="1" required class="mt-0.5">
                        <span>Saya setuju data ini digunakan untuk memproses pesanan saya.</span>
                    </label>

                    <button type="submit"
                            class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-brand-700 text-white font-semibold px-6 py-3 rounded-full hover:bg-brand-800 transition-colors">
                        Buat Pesanan &amp; Bayar
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('js/area-search.js') }}?v={{ filemtime(public_path('js/area-search.js')) }}"></script>
        <script src="{{ asset('js/checkout-shipping.js') }}?v={{ filemtime(public_path('js/checkout-shipping.js')) }}"></script>
        @if($setting->recaptcha_enabled && $setting->recaptcha_site_key)
            <script src="https://www.google.com/recaptcha/api.js?render={{ $setting->recaptcha_site_key }}"></script>
            <script>
                (function () {
                    var form = document.getElementById('checkout-form');
                    form.addEventListener('submit', function (e) {
                        if (form.dataset.recaptchaDone) return;
                        e.preventDefault();
                        grecaptcha.ready(function () {
                            grecaptcha.execute('{{ $setting->recaptcha_site_key }}', { action: 'checkout' }).then(function (token) {
                                document.getElementById('recaptcha_token').value = token;
                                form.dataset.recaptchaDone = '1';
                                form.submit();
                            });
                        });
                    });
                })();
            </script>
        @endif
    @endpush
</x-layouts.app>
