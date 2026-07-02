<x-layouts.admin :setting="\App\Models\Setting::current()" title="Input Stok Massal">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.cabang.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
            &larr; Kembali ke daftar cabang
        </a>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-200">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-teal-100 text-teal-700">
                    <x-icon name="truck" class="w-5 h-5" />
                </span>
                <div>
                    <h2 class="font-semibold text-gray-900">Input Stok Massal — {{ $branch->name }}</h2>
                    <p class="text-xs text-gray-500">Menambah stok (bukan menimpa). Kosongkan / 0 untuk melewati sebuah produk.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.transfer-stok.bulk-store', $branch) }}">
                @csrf
                <div class="p-5 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="py-2">Produk</th>
                                <th class="py-2 text-right">Stok Sekarang</th>
                                <th class="py-2 text-right w-32">Tambah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($products as $product)
                                <tr>
                                    <td class="py-2 font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="py-2 text-right text-gray-500">{{ $product->stockAt($branch) }}</td>
                                    <td class="py-2 text-right">
                                        <input type="number" name="quantities[{{ $product->id }}]" value="0" min="0"
                                               class="w-24 border border-gray-300 rounded-lg px-2 py-1.5 text-sm text-right">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
                        <x-icon name="check-circle" class="w-5 h-5" />
                        Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
