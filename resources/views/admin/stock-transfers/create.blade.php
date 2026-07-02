<x-layouts.admin :setting="\App\Models\Setting::current()" title="Catat Transfer Stok">
    <div class="max-w-md mx-auto">
        <a href="{{ route('admin.transfer-stok.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
            &larr; Kembali ke riwayat transfer
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
                <h2 class="font-semibold text-gray-900">Catat Transfer Stok</h2>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.transfer-stok.store') }}" class="grid gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                        <select name="product_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Cabang</label>
                        <select name="from_branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">— Stok Baru (bukan dari cabang) —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('from_branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ke Cabang</label>
                        <select name="to_branch_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                        <input type="text" name="note" value="{{ old('note') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
                            <x-icon name="check-circle" class="w-5 h-5" />
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
