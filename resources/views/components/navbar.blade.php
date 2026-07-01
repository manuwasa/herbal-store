@props(['setting'])

<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-lg text-brand-700">
            @if($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="h-8 w-auto">
            @endif
            <span>{{ $setting->site_name }}</span>
        </a>

        <nav class="flex items-center gap-6 text-sm font-medium text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-brand-700">Beranda</a>
            <a href="{{ route('catalog.index') }}" class="hover:text-brand-700">Katalog</a>
        </nav>
    </div>
</header>
