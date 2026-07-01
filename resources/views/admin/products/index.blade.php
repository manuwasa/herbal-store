<x-layouts.admin :setting="\App\Models\Setting::current()" title="Produk">
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.produk.create') }}" class="bg-brand-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-brand-700">
            + Tambah Produk
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 overflow-x-auto">
        <table id="products-table" class="w-full text-sm">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.produk.edit', $product) }}" class="text-brand-700 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.produk.destroy', $product) }}" class="inline"
                                  onsubmit="return confirm('Hapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
