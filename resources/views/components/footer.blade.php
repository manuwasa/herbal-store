@props(['setting'])

@php
    $socials = [
        ['url' => $setting->instagram_url, 'label' => 'Instagram'],
        ['url' => $setting->facebook_url, 'label' => 'Facebook'],
        ['url' => $setting->tiktok_profile_url, 'label' => 'TikTok'],
        ['url' => $setting->youtube_url, 'label' => 'YouTube'],
    ];
@endphp

<footer class="bg-brand-900 text-brand-100 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 grid gap-10 sm:grid-cols-3">
        <div>
            <p class="font-display font-semibold text-lg text-white">{{ $setting->site_name }}</p>
            @if($setting->site_tagline)
                <p class="text-sm text-brand-200 mt-2 leading-relaxed">{{ $setting->site_tagline }}</p>
            @endif
        </div>

        @if($setting->contact_email || $setting->contact_phone || $setting->contact_address)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-300 mb-3">Kontak</p>
                <div class="space-y-2.5 text-sm">
                    @if($setting->contact_email)
                        <a href="mailto:{{ $setting->contact_email }}" class="flex items-center gap-2 hover:text-white transition-colors">
                            <x-icon name="mail" class="w-4 h-4 shrink-0 text-brand-400" />
                            {{ $setting->contact_email }}
                        </a>
                    @endif
                    @if($setting->contact_phone)
                        <p class="flex items-center gap-2">
                            <x-icon name="phone" class="w-4 h-4 shrink-0 text-brand-400" />
                            {{ $setting->contact_phone }}
                        </p>
                    @endif
                    @if($setting->contact_address)
                        <p class="flex items-start gap-2">
                            <x-icon name="map-pin" class="w-4 h-4 shrink-0 text-brand-400 mt-0.5" />
                            <span>{{ $setting->contact_address }}</span>
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if(collect($socials)->contains(fn ($s) => filled($s['url'])))
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-brand-300 mb-3">Ikuti Kami</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($socials as $social)
                        @if($social['url'])
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-brand-800 text-sm hover:bg-brand-700 hover:text-white transition-colors">
                                {{ $social['label'] }}
                                <x-icon name="arrow-top-right" class="w-3.5 h-3.5" />
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($setting->footer_text)
        <div class="border-t border-brand-800 py-4 text-center text-xs text-brand-300">
            {{ $setting->footer_text }}
        </div>
    @endif
</footer>
