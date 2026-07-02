@props(['setting'])

<header id="site-header" class="bg-stone-50/90 backdrop-blur-sm border-b border-stone-200 sticky top-0 z-20 transition-all duration-300">
    <div id="site-header-inner" class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4 transition-all duration-300">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            @if($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="h-9 w-auto">
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-full bg-brand-700 text-brand-50">
                    <x-icon name="leaf" class="w-5 h-5" />
                </span>
            @endif
            <span class="font-display font-semibold text-xl text-brand-900 tracking-tight">{{ $setting->site_name }}</span>
        </a>

        <nav class="flex items-center gap-1 text-sm font-medium text-stone-600">
            <a href="{{ route('home') }}"
               class="px-3 py-2 rounded-full transition-colors {{ request()->routeIs('home') ? 'bg-brand-100 text-brand-800' : 'hover:bg-stone-100 hover:text-brand-800' }}">
                Beranda
            </a>
            <a href="{{ route('catalog.index') }}"
               class="px-3 py-2 rounded-full transition-colors {{ request()->routeIs('catalog.*') ? 'bg-brand-100 text-brand-800' : 'hover:bg-stone-100 hover:text-brand-800' }}">
                Katalog
            </a>

            @if($setting->payment_gateway_enabled)
                <a href="{{ route('cart.index') }}" title="Keranjang"
                   class="relative px-3 py-2 rounded-full transition-colors {{ request()->routeIs('cart.*') ? 'bg-brand-100 text-brand-800' : 'hover:bg-stone-100 hover:text-brand-800' }}">
                    <x-icon name="cart" class="w-5 h-5" />
                    @if(($cartCount ?? 0) > 0)
                        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-brand-700 text-white text-[10px] font-bold px-1">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            @endif
        </nav>
    </div>
</header>
