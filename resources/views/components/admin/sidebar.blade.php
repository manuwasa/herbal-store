@props(['setting'])

@php
    use App\Enums\UserRole;

    $role = auth()->user()->role;
    $ownerOnly = [UserRole::Owner];

    // Entries with no 'roles' key are visible to everyone; the controller-level
    // scope handles data restriction for those (Pesanan/Transfer/Laporan/etc).
    $links = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.pesanan.index', 'pattern' => 'admin.pesanan.*', 'icon' => 'shopping-bag', 'label' => 'Pesanan'],
        ['route' => 'admin.produk.index', 'pattern' => 'admin.produk.*', 'icon' => 'box', 'label' => 'Produk', 'roles' => $ownerOnly],
        ['route' => 'admin.kategori.index', 'pattern' => 'admin.kategori.*', 'icon' => 'tag', 'label' => 'Kategori', 'roles' => $ownerOnly],
        ['route' => 'admin.transfer-stok.index', 'pattern' => 'admin.transfer-stok.*', 'icon' => 'truck', 'label' => 'Transfer Stok'],
        ['route' => 'admin.cabang.index', 'pattern' => 'admin.cabang.*', 'icon' => 'map-pin', 'label' => 'Cabang', 'roles' => $ownerOnly],
        ['route' => 'admin.transactions.index', 'pattern' => 'admin.transactions.*', 'icon' => 'receipt', 'label' => 'Riwayat Transaksi'],
        ['route' => 'admin.reports.index', 'pattern' => 'admin.reports.*', 'icon' => 'chart', 'label' => 'Laporan'],
        ['route' => 'admin.pengguna.index', 'pattern' => 'admin.pengguna.*', 'icon' => 'users', 'label' => 'Pengguna', 'roles' => $ownerOnly],
        ['route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Pengaturan', 'roles' => $ownerOnly],
    ];

    $links = array_values(array_filter($links, fn ($link) => ! isset($link['roles']) || in_array($role, $link['roles'], true)));

    // "Paid but not yet processing" = needs attention. Role-scoped automatically.
    $newOrderCount = \App\Models\Order::query()
        ->visibleTo(auth()->user())
        ->where('status', \App\Enums\OrderStatus::Paid)
        ->count();
@endphp

<div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

<aside id="admin-sidebar"
       class="w-60 bg-gray-900 text-gray-300 flex flex-col shrink-0 fixed md:static inset-y-0 left-0 z-40
              -translate-x-full md:translate-x-0 transition-all duration-200 ease-in-out">

    {{-- Mobile header: branding + close button --}}
    <div class="md:hidden flex items-center gap-2.5 px-5 py-5 border-b border-gray-800">
        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-600 text-white shrink-0 overflow-hidden">
            @if($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="w-full h-full object-cover">
            @else
                <x-icon name="leaf" class="w-5 h-5" />
            @endif
        </span>
        <div class="leading-tight overflow-hidden whitespace-nowrap flex-1 min-w-0">
            <p class="text-white font-semibold text-sm whitespace-nowrap">Admin Panel</p>
            <p class="text-gray-500 text-xs whitespace-nowrap">{{ $setting->site_name }}</p>
        </div>
        <button type="button" id="sidebar-close-toggle" class="shrink-0 p-1 text-gray-400 hover:text-white">
            <x-icon name="x-mark" class="w-5 h-5" />
        </button>
    </div>

    {{-- Desktop header: branding + collapse toggle (the whole row is the toggle) --}}
    <button type="button" id="sidebar-collapse-toggle" title="Ciutkan sidebar"
            class="sidebar-item hidden md:flex items-center gap-2.5 px-5 py-5 border-b border-gray-800 hover:bg-gray-800/60 transition-colors w-full text-left">
        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-600 text-white shrink-0 overflow-hidden">
            @if($setting->logo_path)
                <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="w-full h-full object-cover">
            @else
                <x-icon name="leaf" class="w-5 h-5" />
            @endif
        </span>
        <div class="leading-tight sidebar-label overflow-hidden whitespace-nowrap flex-1 min-w-0">
            <p class="text-white font-semibold text-sm whitespace-nowrap">Admin Panel</p>
            <p class="text-gray-500 text-xs whitespace-nowrap">{{ $setting->site_name }}</p>
        </div>
        <x-icon name="collapse" class="w-4 h-4 shrink-0 text-gray-500 sidebar-label sidebar-collapse-icon" />
    </button>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto overflow-x-hidden">
        @foreach($links as $link)
            <a href="{{ route($link['route']) }}" title="{{ $link['label'] }}"
               class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors whitespace-nowrap
                      {{ request()->routeIs($link['pattern']) ? 'bg-brand-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <x-icon :name="$link['icon']" class="w-5 h-5 shrink-0" />
                <span class="sidebar-label overflow-hidden">{{ $link['label'] }}</span>
                @if($link['route'] === 'admin.pesanan.index' && $newOrderCount > 0)
                    <span class="sidebar-label ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-400 text-amber-950 text-xs font-bold">
                        {{ $newOrderCount }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-3 border-t border-gray-800">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" title="Logout"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors whitespace-nowrap">
                <x-icon name="logout" class="w-5 h-5 shrink-0" />
                <span class="sidebar-label">Logout</span>
            </button>
        </form>
    </div>
</aside>
