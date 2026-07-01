<x-layouts.admin :setting="\App\Models\Setting::current()" title="Edit Kategori">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.kategori.update', $category) }}">
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
</x-layouts.admin>
