<x-layouts.admin :setting="\App\Models\Setting::current()" title="Produk">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-100 text-brand-700">
                    <x-icon name="box" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Daftar Produk</h2>
            </div>
            <div class="flex items-center gap-2">
                <div id="products-table-search-slot" class="flex-1 sm:flex-initial"></div>
                <a href="{{ route('admin.produk.create') }}"
                   class="shrink-0 inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-700 whitespace-nowrap">
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Produk
                </a>
            </div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="products-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="no-sort"></th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Top Pick</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="w-10 h-10 mx-auto rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="box" class="w-5 h-5 text-gray-300" />
                                    @endif
                                </div>
                            </td>
                            <td class="font-medium text-gray-900">{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge badge-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_top_pick)
                                    <x-icon name="star-solid" class="w-5 h-5 text-amber-400 mx-auto" />
                                @else
                                    <x-icon name="star" class="w-5 h-5 text-gray-300 mx-auto" />
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.produk.edit', $product) }}" class="icon-btn" title="Edit">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.produk.destroy', $product) }}" class="inline"
                                      onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn-danger" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
