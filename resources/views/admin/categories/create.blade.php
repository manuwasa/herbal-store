<x-layouts.admin :setting="\App\Models\Setting::current()" title="Tambah Kategori">
    <div class="max-w-md">
        <a href="{{ route('admin.kategori.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
            &larr; Kembali ke daftar kategori
        </a>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-200">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-700">
                    <x-icon name="plus" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Tambah Kategori</h2>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.kategori.store') }}">
                    @include('admin.categories._form')
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
