<x-layouts.admin :setting="\App\Models\Setting::current()" title="Edit Produk">
    <div class="max-w-2xl">
        <a href="{{ route('admin.produk.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
            &larr; Kembali ke daftar produk
        </a>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-200">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-brand-100 text-brand-700">
                    <x-icon name="edit" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Edit Produk</h2>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.produk.update', $product) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('admin.products._form')
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
