<?php

namespace App\Services\Branches;

use App\Exceptions\InsufficientStockException;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    /**
     * Record a stock movement. $from = null means new stock entering the system
     * (initial injection), not a branch-to-branch transfer.
     */
    public function transfer(
        ?Branch $from,
        Branch $to,
        Product $product,
        int $quantity,
        User $createdBy,
        ?string $note = null
    ): StockTransfer {
        if ($quantity <= 0) {
            throw new InsufficientStockException('Jumlah transfer harus lebih dari 0.');
        }

        return DB::transaction(function () use ($from, $to, $product, $quantity, $createdBy, $note) {
            if ($from !== null) {
                // Pessimistic lock on the source row — the one place worth guarding
                // against a concurrent transfer driving stock negative.
                $fromStock = BranchStock::query()
                    ->where('branch_id', $from->id)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (! $fromStock || $fromStock->stock < $quantity) {
                    throw new InsufficientStockException(
                        "Stok di cabang {$from->name} tidak cukup untuk transfer ini."
                    );
                }

                $fromStock->decrement('stock', $quantity);
            }

            BranchStock::query()
                ->firstOrCreate(['branch_id' => $to->id, 'product_id' => $product->id])
                ->increment('stock', $quantity);

            return StockTransfer::create([
                'from_branch_id' => $from?->id,
                'to_branch_id' => $to->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'created_by' => $createdBy->id,
                'note' => $note,
                'created_at' => now(),
            ]);
        });
    }
}
