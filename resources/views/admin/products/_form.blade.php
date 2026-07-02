@csrf

<div class="grid gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" step="0.01" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berat (gram)</label>
            <input type="number" name="weight" value="{{ old('weight', $product->weight ?? '') }}" min="1" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Wajib — dipakai untuk hitung ongkir otomatis.</p>
        </div>
    </div>

    @if(!empty($product) && $product->exists)
        <div class="rounded-lg bg-gray-50 border border-gray-200 px-3 py-2.5 text-sm text-gray-600">
            <span class="font-medium text-gray-800">Stok saat ini:</span> {{ $product->totalStock() }} unit (semua cabang).
            Ubah stok lewat menu <a href="{{ route('admin.transfer-stok.index') }}" class="text-brand-700 hover:underline">Transfer Stok</a>.
        </div>
    @else
        <div class="rounded-lg bg-gray-50 border border-gray-200 px-3 py-2.5 text-sm text-gray-600">
            Stok diatur per cabang lewat menu <span class="font-medium">Transfer Stok</span> setelah produk disimpan.
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
        @if(!empty($product) && $product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" class="w-24 h-24 object-cover rounded-lg mb-2">
        @endif
        <input type="file" name="image" accept="image/*" class="file-input">
    </div>

    <div class="border-t border-gray-200 pt-4">
        <p class="text-sm font-medium text-gray-700 mb-2">Link Order (opsional)</p>

        <div class="space-y-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Shopee URL</label>
                <input type="url" name="shopee_url" value="{{ old('shopee_url', $product->shopee_url ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">TikTok URL</label>
                <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $product->tiktok_url ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Order Now URL (link umum lainnya)</label>
                <input type="url" name="order_now_url" value="{{ old('order_now_url', $product->order_now_url ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
        Aktifkan produk (tampil di katalog)
    </label>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_top_pick" value="1" @checked(old('is_top_pick', $product->is_top_pick ?? false))>
        Tandai sebagai Top Pick (tampil di slider beranda)
    </label>

    <div>
        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan
        </button>
    </div>
</div>
