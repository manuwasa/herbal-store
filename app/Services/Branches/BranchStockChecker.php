<?php

namespace App\Services\Branches;

use App\Models\Branch;
use Illuminate\Support\Collection;

class BranchStockChecker
{
    /**
     * Can this branch fully cover every line in the cart?
     *
     * @param  Collection<int, \App\Services\Cart\CartLine>  $cartLines
     */
    public function canFulfill(Branch $branch, Collection $cartLines): bool
    {
        foreach ($cartLines as $line) {
            if ($line->product->stockAt($branch) < $line->quantity) {
                return false;
            }
        }

        return true;
    }
}
