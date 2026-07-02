<?php

namespace App\Services\Branches;

use App\Models\Branch;
use Illuminate\Support\Collection;

/**
 * Picks the branch that should fulfil an order for a given destination.
 *
 * This is a RANKING by administrative area (district -> city -> province ->
 * anywhere), NOT a true GPS distance — Biteship gives us named areas, not
 * coordinates, and the buyer never supplies a precise point. Two adjacent
 * districts in different cities can therefore rank as "different city" even if
 * geographically close; a known, accepted limitation of hierarchy ranking.
 */
class BranchLocator
{
    public function __construct(private BranchStockChecker $stockChecker)
    {
    }

    /**
     * @param  Collection<int, \App\Services\Cart\CartLine>  $cartLines
     */
    public function findFulfillingBranch(
        ?string $destProvince,
        ?string $destCity,
        ?string $destDistrict,
        Collection $cartLines
    ): ?Branch {
        $candidates = Branch::query()
            ->active()
            ->whereNotNull('area_id')
            ->where('area_id', '!=', '')
            ->get();

        // Rank most-specific match first; deterministic id tie-break within a tier.
        $ranked = $candidates->sort(function (Branch $a, Branch $b) use ($destProvince, $destCity, $destDistrict) {
            $rankA = $this->rank($a, $destProvince, $destCity, $destDistrict);
            $rankB = $this->rank($b, $destProvince, $destCity, $destDistrict);

            return $rankA <=> $rankB ?: $a->id <=> $b->id;
        });

        foreach ($ranked as $branch) {
            if ($this->stockChecker->canFulfill($branch, $cartLines)) {
                return $branch;
            }
        }

        return null;
    }

    private function rank(Branch $branch, ?string $province, ?string $city, ?string $district): int
    {
        if ($district && $branch->district_name && $this->eq($branch->district_name, $district)) {
            return 0;
        }
        if ($city && $branch->city_name && $this->eq($branch->city_name, $city)) {
            return 1;
        }
        if ($province && $branch->province_name && $this->eq($branch->province_name, $province)) {
            return 2;
        }

        return 3;
    }

    private function eq(string $a, string $b): bool
    {
        return mb_strtolower(trim($a)) === mb_strtolower(trim($b));
    }
}
