<x-layouts.admin :setting="\App\Models\Setting::current()" title="Kategori">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-700">
                    <x-icon name="tag" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Daftar Kategori</h2>
            </div>
            <a href="{{ route('admin.kategori.create') }}"
               class="inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-700">
                <x-icon name="plus" class="w-4 h-4" />
                Tambah Kategori
            </a>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="categories-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah Produk</th>
                        <th>Status</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td class="font-medium text-gray-900">{{ $category->name }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge badge-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.kategori.edit', $category) }}" class="icon-btn" title="Edit">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.kategori.destroy', $category) }}" class="inline"
                                      onsubmit="return confirm('Hapus kategori ini?');">
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
