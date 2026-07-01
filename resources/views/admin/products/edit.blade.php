<x-layouts.admin :setting="\App\Models\Setting::current()" title="Edit Produk">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.produk.update', $product) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.products._form')
        </form>
    </div>
</x-layouts.admin>
