<x-layouts.admin :setting="$setting" title="Pengaturan">
    <div class="max-w-2xl mx-auto">
        @if($errors->any())
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <x-icon name="alert" class="w-5 h-5 shrink-0" />
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="leaf" class="w-5 h-5 text-brand-600" />
                Branding
            </h2>
            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Website</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $setting->site_tagline) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Website (SEO)</label>
                    <textarea name="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('site_description', $setting->site_description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if($setting->logo_path)
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" class="h-10 mb-2">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="file-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        @if($setting->favicon_path)
                            <img src="{{ asset('storage/' . $setting->favicon_path) }}" class="h-10 mb-2">
                        @endif
                        <input type="file" name="favicon" accept="image/*" class="file-input">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Default Produk (jika produk belum punya foto)</label>
                    @if($setting->product_placeholder_image_path)
                        <img src="{{ asset('storage/' . $setting->product_placeholder_image_path) }}" class="h-16 mb-2 rounded">
                    @endif
                    <input type="file" name="product_placeholder_image" accept="image/*" class="file-input">
                    <p class="text-xs text-gray-400 mt-1">Kalau tidak diisi, produk tanpa foto akan menampilkan icon generik.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Footer</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $setting->footer_text) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="megaphone" class="w-5 h-5 text-brand-600" />
                Banner Beranda
            </h2>
            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Badge (opsional)</label>
                    <input type="text" name="banner_badge_text" value="{{ old('banner_badge_text', $setting->banner_badge_text) }}"
                           placeholder="mis. 100% Herbal Alami"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Badge kecil di atas judul banner. Kosongkan untuk menyembunyikan.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Banner</label>
                    <input type="text" name="banner_heading" value="{{ old('banner_heading', $setting->banner_heading) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Judul Banner</label>
                    <input type="text" name="banner_subheading" value="{{ old('banner_subheading', $setting->banner_subheading) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Banner</label>
                    @if($setting->banner_image_path)
                        <img src="{{ asset('storage/' . $setting->banner_image_path) }}" class="h-16 mb-2 rounded">
                    @endif
                    <input type="file" name="banner_image" accept="image/*" class="file-input">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="phone" class="w-5 h-5 text-brand-600" />
                Kontak & WhatsApp
            </h2>
            <div class="grid gap-4">
                <div class="rounded-lg bg-blue-50 border border-blue-200 px-3 py-2.5 text-xs text-blue-700">
                    Nomor WhatsApp kini diatur <span class="font-medium">per cabang</span> di menu
                    <a href="{{ route('admin.cabang.index') }}" class="underline">Cabang</a>.
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Pesan WhatsApp (Tanya Produk)</label>
                    <textarea name="whatsapp_message_template" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('whatsapp_message_template', $setting->whatsapp_message_template) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Gunakan {product} dan {url} sebagai placeholder.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Pesan WhatsApp (Status Pesanan)</label>
                    <textarea name="whatsapp_order_message_template" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('whatsapp_order_message_template', $setting->whatsapp_order_message_template) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Gunakan {order_number} dan {status_url} sebagai placeholder.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="contact_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('contact_address', $setting->contact_address) }}</textarea>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="tag" class="w-5 h-5 text-brand-600" />
                Sosial Media (Footer)
            </h2>
            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TikTok Profile URL</label>
                    <input type="url" name="tiktok_profile_url" value="{{ old('tiktok_profile_url', $setting->tiktok_profile_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="shopping-bag" class="w-5 h-5 text-brand-600" />
                Payment Gateway (Midtrans)
            </h2>
            <div class="grid gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="payment_gateway_enabled" value="1" @checked(old('payment_gateway_enabled', $setting->payment_gateway_enabled))>
                    Aktifkan checkout &amp; pembayaran online (tampilkan keranjang di situs)
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="midtrans_is_production" value="1" @checked(old('midtrans_is_production', $setting->midtrans_is_production))>
                    Mode produksi (matikan untuk sandbox/testing)
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Key</label>
                    <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $setting->midtrans_client_key) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Server Key</label>
                    <input type="password" name="midtrans_server_key" autocomplete="new-password"
                           placeholder="{{ $setting->hasMidtransServerKey() ? '•••••••• (klik untuk mengganti)' : '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Rahasia — kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="truck" class="w-5 h-5 text-brand-600" />
                Ongkir Otomatis (Biteship)
            </h2>
            <div class="grid gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="shipping_enabled" value="1" @checked(old('shipping_enabled', $setting->shipping_enabled))>
                    Aktifkan hitung ongkir otomatis di checkout
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="biteship_is_production" value="1" @checked(old('biteship_is_production', $setting->biteship_is_production))>
                    Mode produksi (label saja — Biteship pakai satu URL untuk sandbox &amp; produksi)
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kurir yang didukung (pisahkan koma)</label>
                    <input type="text" name="shipping_couriers" value="{{ old('shipping_couriers', $setting->shipping_couriers) }}"
                           placeholder="jne,jnt,sicepat,anteraja"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="password" name="biteship_api_key" autocomplete="new-password"
                           placeholder="{{ $setting->hasBiteshipApiKey() ? '•••••••• (klik untuk mengganti)' : '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Rahasia — kosongkan jika tidak ingin mengubah. Alamat asal diatur per cabang.</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="alert" class="w-5 h-5 text-brand-600" />
                Keamanan Checkout (reCAPTCHA v3)
            </h2>
            <div class="grid gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="recaptcha_enabled" value="1" @checked(old('recaptcha_enabled', $setting->recaptcha_enabled))>
                    Aktifkan proteksi bot di form checkout
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Key</label>
                    <input type="text" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $setting->recaptcha_site_key) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                    <input type="password" name="recaptcha_secret_key" autocomplete="new-password"
                           placeholder="{{ $setting->hasRecaptchaSecretKey() ? '•••••••• (klik untuk mengganti)' : '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Rahasia — kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>
        </section>

        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan Pengaturan
        </button>
        </form>
    </div>
</x-layouts.admin>
