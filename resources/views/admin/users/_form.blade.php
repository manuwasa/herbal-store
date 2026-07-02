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
        <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
        <select name="role" id="user-role-select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @foreach($roles as $roleOption)
                <option value="{{ $roleOption->value }}" @selected(old('role', $user->role->value ?? 'owner') === $roleOption->value)>
                    {{ $roleOption->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="user-branch-wrapper">
        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
        <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— Pilih cabang —</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $user->branch_id ?? null) == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">Wajib untuk Staf Cabang. Diabaikan untuk Pemilik.</p>
    </div>

    <div>
        <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-700">
            <x-icon name="check-circle" class="w-5 h-5" />
            Simpan
        </button>
    </div>
</div>

<script>
    (function () {
        var roleSelect = document.getElementById('user-role-select');
        var branchWrapper = document.getElementById('user-branch-wrapper');
        function toggle() {
            branchWrapper.style.display = roleSelect.value === 'branch_staff' ? '' : 'none';
        }
        roleSelect.addEventListener('change', toggle);
        toggle();
    })();
</script>
