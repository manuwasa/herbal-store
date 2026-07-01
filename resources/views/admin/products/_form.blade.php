@csrf

<div class="grid gap-4 max-w-2xl">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
        @if(!empty($product) && $product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" class="w-24 h-24 object-cover rounded-lg mb-2">
        @endif
        <input type="file" name="image" accept="image/*" class="w-full text-sm">
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

    <div>
        <button type="submit" class="bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            Simpan
        </button>
    </div>
</div>
