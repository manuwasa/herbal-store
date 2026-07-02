<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::query()->with('branch')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'branches' => Branch::query()->orderBy('name')->get(),
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);

        User::query()->create($data);

        return redirect()->route('admin.pengguna.index')->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'branches' => Branch::query()->orderBy('name')->get(),
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        if (filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);

            // A stolen session stops working the moment the password changes.
            if ($user->is($request->user())) {
                Auth::logoutOtherDevices($request->input('password'));
            }
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.pengguna.index')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }

        // The real lockout risk is zero owners, not zero users.
        if ($user->role === UserRole::Owner
            && User::query()->where('role', UserRole::Owner->value)->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus satu-satunya akun Pemilik yang tersisa.']);
        }

        $user->delete();

        return redirect()->route('admin.pengguna.index')->with('status', 'Pengguna berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'branch_id' => ['nullable', 'required_if:role,' . UserRole::BranchStaff->value, 'exists:branches,id'],
        ]);

        // An Owner is never scoped to a branch, regardless of what was submitted.
        if ($data['role'] === UserRole::Owner->value) {
            $data['branch_id'] = null;
        }

        return $data;
    }
}
