@php
    $links = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.produk.index', 'pattern' => 'admin.produk.*', 'icon' => 'box', 'label' => 'Produk'],
        ['route' => 'admin.kategori.index', 'pattern' => 'admin.kategori.*', 'icon' => 'tag', 'label' => 'Kategori'],
        ['route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Pengaturan'],
    ];
@endphp

<aside class="w-60 bg-gray-900 text-gray-300 flex flex-col shrink-0">
    <div class="px-5 py-5 flex items-center gap-2.5 border-b border-gray-800">
        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-600 text-white">
            <x-icon name="leaf" class="w-5 h-5" />
        </span>
        <div class="leading-tight">
            <p class="text-white font-semibold text-sm">Admin Panel</p>
            <p class="text-gray-500 text-xs">Herbal Store</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                      {{ request()->routeIs($link['pattern']) ? 'bg-brand-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <x-icon :name="$link['icon']" class="w-5 h-5 shrink-0" />
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-4 border-t border-gray-800">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                <x-icon name="logout" class="w-5 h-5 shrink-0" />
                Logout
            </button>
        </form>
    </div>
</aside>
