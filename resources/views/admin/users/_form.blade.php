@csrf

<div class="grid gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" {{ empty($user) ? 'required' : '' }}
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        @if(!empty($user))
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" {{ empty($user) ? 'required' : '' }}
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan
        </button>
    </div>
</div>
