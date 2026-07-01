@csrf

<div class="grid gap-4 max-w-md">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
        Aktifkan kategori
    </label>

    <div>
        <button type="submit" class="bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            Simpan
        </button>
    </div>
</div>
