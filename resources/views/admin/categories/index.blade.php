<x-layouts.admin :setting="\App\Models\Setting::current()" title="Kategori">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.kategori.create') }}" class="bg-brand-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-brand-700">
            + Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 overflow-x-auto">
        <table id="categories-table" class="w-full text-sm">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jumlah Produk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.kategori.edit', $category) }}" class="text-brand-700 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.kategori.destroy', $category) }}" class="inline"
                                  onsubmit="return confirm('Hapus kategori ini?');">
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
