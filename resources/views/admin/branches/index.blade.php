<x-layouts.admin :setting="\App\Models\Setting::current()" title="Cabang">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-100 text-sky-700">
                    <x-icon name="map-pin" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Daftar Cabang</h2>
            </div>
            <div class="flex items-center gap-2">
                <div id="branches-table-search-slot" class="flex-1 sm:flex-initial"></div>
                <a href="{{ route('admin.cabang.create') }}"
                   class="shrink-0 inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-700 whitespace-nowrap">
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Cabang
                </a>
            </div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="branches-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Area Kirim</th>
                        <th>WhatsApp</th>
                        <th>Produk</th>
                        <th>Status</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                        <tr>
                            <td class="font-medium text-gray-900">
                                {{ $branch->name }}
                                @if($branch->is_default)
                                    <span class="badge bg-brand-100 text-brand-700 ml-1">Default</span>
                                @endif
                            </td>
                            <td>{{ $branch->code }}</td>
                            <td>{{ $branch->area_label ?: '—' }}</td>
                            <td>{{ $branch->whatsapp_number ?: '—' }}</td>
                            <td>{{ $branch->branch_stocks_count }}</td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge badge-active">Aktif</span>
                                @else
                                    <span class="badge badge-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.transfer-stok.bulk-create', $branch) }}" class="icon-btn" title="Input stok massal">
                                    <x-icon name="truck" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('admin.cabang.edit', $branch) }}" class="icon-btn" title="Edit">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </a>
                                @unless($branch->is_default)
                                    <form method="POST" action="{{ route('admin.cabang.set-default', $branch) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="icon-btn" title="Jadikan default">
                                            <x-icon name="star" class="w-4 h-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.cabang.destroy', $branch) }}" class="inline"
                                          onsubmit="return confirm('Hapus cabang ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn icon-btn-danger" title="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
