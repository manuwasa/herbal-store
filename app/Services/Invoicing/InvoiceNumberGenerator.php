<?php

namespace App\Services\Invoicing;

use App\Models\InvoiceCounter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    /**
     * Format: INV/{year}/{month}/{seq}, resetting monthly. $at defaults to now()
     * for real orders; OrderSeeder passes each order's backdated placement time
     * so demo data gets historically-consistent numbers instead of every seeded
     * order landing in the current real month.
     */
    public function generate(?Carbon $at = null): string
    {
        $at ??= now();
        $period = $at->format('Y-m');

        return DB::transaction(function () use ($period, $at) {
            $counter = InvoiceCounter::query()->where('period', $period)->lockForUpdate()->first();

            if (! $counter) {
                // lockForUpdate() can't lock a row that doesn't exist yet, so a
                // concurrent request may create this period's row first — the
                // unique constraint on `period` turns that into a query
                // exception here, which just means we re-read the now-existing
                // row under lock instead of treating it as a real failure.
                try {
                    $counter = InvoiceCounter::create(['period' => $period, 'last_number' => 0]);
                } catch (QueryException $e) {
                    $counter = InvoiceCounter::query()->where('period', $period)->lockForUpdate()->firstOrFail();
                }
            }

            $counter->increment('last_number');

            return sprintf('INV/%s/%s/%04d', $at->format('Y'), $at->format('m'), $counter->last_number);
        });
    }
}
