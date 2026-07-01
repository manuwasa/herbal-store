<x-layouts.admin :setting="\App\Models\Setting::current()" title="Tambah Kategori">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.kategori.store') }}">
            @include('admin.categories._form')
        </form>
    </div>
</x-layouts.admin>
