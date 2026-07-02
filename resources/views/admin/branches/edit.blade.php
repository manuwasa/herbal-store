<x-layouts.admin :setting="\App\Models\Setting::current()" title="Edit Cabang">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('admin.cabang.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
            &larr; Kembali ke daftar cabang
        </a>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-200">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-100 text-sky-700">
                    <x-icon name="edit" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Edit Cabang</h2>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.cabang.update', $branch) }}">
                    @method('PUT')
                    @include('admin.branches._form')
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
