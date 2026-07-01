@props(['setting'])

<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-6xl mx-auto px-4 py-8 grid gap-6 sm:grid-cols-2">
        <div>
            <p class="font-semibold text-gray-900">{{ $setting->site_name }}</p>
            @if($setting->site_tagline)
                <p class="text-sm text-gray-500 mt-1">{{ $setting->site_tagline }}</p>
            @endif
        </div>

        <div class="text-sm text-gray-600 space-y-1 sm:text-right">
            @if($setting->contact_email)
                <p><a href="mailto:{{ $setting->contact_email }}" class="hover:text-brand-700">{{ $setting->contact_email }}</a></p>
            @endif
            @if($setting->contact_phone)
                <p>{{ $setting->contact_phone }}</p>
            @endif
            @if($setting->contact_address)
                <p>{{ $setting->contact_address }}</p>
            @endif

            <div class="flex gap-3 sm:justify-end pt-1">
                @if($setting->instagram_url)
                    <a href="{{ $setting->instagram_url }}" target="_blank" rel="noopener" class="hover:text-brand-700">Instagram</a>
                @endif
                @if($setting->facebook_url)
                    <a href="{{ $setting->facebook_url }}" target="_blank" rel="noopener" class="hover:text-brand-700">Facebook</a>
                @endif
                @if($setting->tiktok_profile_url)
                    <a href="{{ $setting->tiktok_profile_url }}" target="_blank" rel="noopener" class="hover:text-brand-700">TikTok</a>
                @endif
                @if($setting->youtube_url)
                    <a href="{{ $setting->youtube_url }}" target="_blank" rel="noopener" class="hover:text-brand-700">YouTube</a>
                @endif
            </div>
        </div>
    </div>

    @if($setting->footer_text)
        <div class="border-t border-gray-100 py-4 text-center text-xs text-gray-400">
            {{ $setting->footer_text }}
        </div>
    @endif
</footer>
