<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branches.index', [
            'branches' => Branch::query()->withCount('branchStocks')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Branch::query()->create($data);

        return redirect()->route('admin.cabang.index')->with('status', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', ['branch' => $branch]);
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($this->validated($request, $branch));

        return redirect()->route('admin.cabang.index')->with('status', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->is_default) {
            return back()->withErrors(['branch' => 'Cabang default tidak bisa dihapus. Tetapkan cabang lain sebagai default dulu.']);
        }

        if ($branch->branchStocks()->where('stock', '>', 0)->exists()) {
            return back()->withErrors(['branch' => 'Cabang masih memiliki stok. Pindahkan stok dulu sebelum menghapus.']);
        }

        $activeStatuses = [OrderStatus::PendingPayment, OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped];
        if ($branch->orders()->whereIn('status', array_map(fn ($s) => $s->value, $activeStatuses))->exists()) {
            return back()->withErrors(['branch' => 'Cabang masih memiliki pesanan berjalan.']);
        }

        $branch->delete();

        return redirect()->route('admin.cabang.index')->with('status', 'Cabang berhasil dihapus.');
    }

    public function setDefault(Branch $branch)
    {
        DB::transaction(function () use ($branch) {
            Branch::query()->where('is_default', true)->update(['is_default' => false]);
            $branch->update(['is_default' => true, 'is_active' => true]);
        });

        return back()->with('status', "Cabang \"{$branch->name}\" ditetapkan sebagai default.");
    }

    private function validated(Request $request, ?Branch $branch = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:20', Rule::unique('branches', 'code')->ignore($branch?->id)],
            'address_detail' => ['required', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'area_id' => ['nullable', 'string', 'max:100'],
            'area_label' => ['nullable', 'string', 'max:255'],
            'province_name' => ['nullable', 'string', 'max:150'],
            'city_name' => ['nullable', 'string', 'max:150'],
            'district_name' => ['nullable', 'string', 'max:150'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
