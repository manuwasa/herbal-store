<aside class="w-56 bg-gray-900 text-gray-300 flex flex-col shrink-0">
    <div class="px-5 py-4 text-white font-semibold border-b border-gray-800">
        Admin Panel
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('admin.dashboard') }}"
           class="block px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.produk.index') }}"
           class="block px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.produk.*') ? 'bg-gray-800 text-white' : '' }}">
            Produk
        </a>
        <a href="{{ route('admin.kategori.index') }}"
           class="block px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-800 text-white' : '' }}">
            Kategori
        </a>
        <a href="{{ route('admin.settings.edit') }}"
           class="block px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : '' }}">
            Pengaturan
        </a>
    </nav>
</aside>
