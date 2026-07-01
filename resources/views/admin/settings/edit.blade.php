<x-layouts.admin :setting="$setting" title="Pengaturan">
    @if($errors->any())
        <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            <x-icon name="alert" class="w-5 h-5 shrink-0" />
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8 max-w-2xl">
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
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        @if($setting->favicon_path)
                            <img src="{{ asset('storage/' . $setting->favicon_path) }}" class="h-10 mb-2">
                        @endif
                        <input type="file" name="favicon" accept="image/*" class="w-full text-sm">
                    </div>
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
                    <input type="file" name="banner_image" accept="image/*" class="w-full text-sm">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
                <x-icon name="phone" class="w-5 h-5 text-brand-600" />
                Kontak & WhatsApp
            </h2>
            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Admin</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"
                           placeholder="6281234567890"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Pesan WhatsApp</label>
                    <textarea name="whatsapp_message_template" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('whatsapp_message_template', $setting->whatsapp_message_template) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Gunakan {product} dan {url} sebagai placeholder.</p>
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

        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan Pengaturan
        </button>
    </form>
</x-layouts.admin>
