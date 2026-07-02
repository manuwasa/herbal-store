<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Services\Branches\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function __construct(private StockTransferService $transfers)
    {
    }

    public function index()
    {
        return view('admin.stock-transfers.index', [
            'transfers' => StockTransfer::query()
                ->with(['fromBranch', 'toBranch', 'product', 'createdBy'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.stock-transfers.create', [
            'branches' => Branch::query()->active()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_branch_id' => ['nullable', 'exists:branches,id'],
            'to_branch_id' => ['required', 'exists:branches,id', 'different:from_branch_id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->transfers->transfer(
                $data['from_branch_id'] ? Branch::find($data['from_branch_id']) : null,
                Branch::findOrFail($data['to_branch_id']),
                Product::findOrFail($data['product_id']),
                (int) $data['quantity'],
                $request->user(),
                $data['note'] ?? null,
            );
        } catch (InsufficientStockException $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('admin.transfer-stok.index')->with('status', 'Transfer stok berhasil dicatat.');
    }

    // ---- Bulk initial-stock entry --------------------------------------

    public function bulkCreate(Branch $branch)
    {
        return view('admin.stock-transfers.bulk', [
            'branch' => $branch,
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function bulkStore(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $count = 0;

        DB::transaction(function () use ($data, $branch, $request, &$count) {
            foreach ($data['quantities'] as $productId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) {
                    continue; // additive bulk-receive; zeros skipped, never overwrite
                }

                $product = Product::find($productId);
                if (! $product) {
                    continue;
                }

                $this->transfers->transfer(
                    null, // new stock entering the system
                    $branch,
                    $product,
                    $qty,
                    $request->user(),
                    'Input stok awal (massal)',
                );
                $count++;
            }
        });

        return redirect()->route('admin.transfer-stok.index')
            ->with('status', "Stok awal untuk {$count} produk berhasil dicatat di cabang {$branch->name}.");
    }
}
