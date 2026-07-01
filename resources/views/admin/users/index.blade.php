<x-layouts.admin :setting="\App\Models\Setting::current()" title="Pengguna">
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-100 text-sky-700">
                    <x-icon name="users" class="w-5 h-5" />
                </span>
                <h2 class="font-semibold text-gray-900">Daftar Pengguna</h2>
            </div>
            <div class="flex items-center gap-2">
                <div id="users-table-search-slot" class="flex-1 sm:flex-initial"></div>
                <a href="{{ route('admin.pengguna.create') }}"
                   class="shrink-0 inline-flex items-center gap-1.5 bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-700 whitespace-nowrap">
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Pengguna
                </a>
            </div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="users-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="font-medium text-gray-900">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.pengguna.edit', $user) }}" class="icon-btn" title="Edit">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </a>
                                @unless($user->is(request()->user()))
                                    <form method="POST" action="{{ route('admin.pengguna.destroy', $user) }}" class="inline"
                                          onsubmit="return confirm('Hapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn icon-btn-danger" title="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
