<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionHistoryController extends Controller
{
    /**
     * A bank-statement-style list of every payment event (one row per attempt,
     * not per order). Read-only. Scoped by role exactly like Pesanan/Laporan.
     */
    public function index(Request $request)
    {
        $transactions = PaymentTransaction::query()
            ->whereHas('order', fn ($q) => $q->visibleTo($request->user()))
            ->with('order.branch')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('branch_id'), fn ($q) => $q->whereHas(
                'order',
                fn ($o) => $o->where('branch_id', $request->integer('branch_id'))
            ))
            ->when($request->filled('dari'), fn ($q) => $q->where('created_at', '>=', Carbon::parse($request->query('dari'))->startOfDay()))
            ->when($request->filled('sampai'), fn ($q) => $q->where('created_at', '<=', Carbon::parse($request->query('sampai'))->endOfDay()))
            ->latest('id')
            ->get();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'branches' => Branch::query()->orderBy('name')->get(),
        ]);
    }
}
