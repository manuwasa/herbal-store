@csrf

<div class="grid gap-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cabang</label>
            <input type="text" name="name" value="{{ old('name', $branch->name ?? '') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
            <input type="text" name="code" value="{{ old('code', $branch->code ?? '') }}" required
                   placeholder="JKT-SEL"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
        <textarea name="address_detail" rows="2" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address_detail', $branch->address_detail ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon (internal)</label>
            <input type="text" name="phone" value="{{ old('phone', $branch->phone ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp (pelanggan)</label>
            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $branch->whatsapp_number ?? '') }}"
                   placeholder="6281234567890"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div class="border-t border-gray-200 pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Area Asal Pengiriman (Biteship)</label>
        <p class="text-xs text-gray-400 mb-2">Ketik kota/kecamatan cabang ini, lalu pilih. Dipakai sebagai titik asal hitung ongkir. Kosongkan kalau belum siap — checkout tetap jalan (ongkir diatur via WhatsApp).</p>
        <x-area-search-field name="area" :labelValue="$branch->area_label ?? ''" />
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $branch->is_active ?? true))>
        Aktifkan cabang
    </label>

    <div>
        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan
        </button>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/area-search.js') }}?v={{ filemtime(public_path('js/area-search.js')) }}"></script>
@endpush
