<x-layouts.admin :setting="\App\Models\Setting::current()" title="Tambah Produk">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
            @include('admin.products._form')
        </form>
    </div>
</x-layouts.admin>
