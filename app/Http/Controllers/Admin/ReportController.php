<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $orders = $this->scopedPaidOrders($request, $from, $to)->get();
        $orderIds = $orders->pluck('id');

        return view('admin.reports.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'branchId' => $request->integer('branch_id') ?: null,
            'branches' => Branch::query()->orderBy('name')->get(),
            'totalRevenue' => $orders->sum('total'),
            'orderCount' => $orders->count(),
            'revenueByDay' => $this->revenueByDay($orders),
            'topProducts' => $this->topProducts($orderIds),
            'statusCounts' => $this->statusCounts($request, $from, $to),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $orders = $this->scopedPaidOrders($request, $from, $to)->get();

        $which = $request->query('type', 'harian');
        $filename = "laporan-{$which}-{$from->toDateString()}-{$to->toDateString()}.csv";

        if ($which === 'produk') {
            $rows = $this->topProducts($orders->pluck('id'), null);
            $header = ['Produk', 'Jumlah Terjual', 'Pendapatan'];
            $mapper = fn ($r) => [$r->product_name, $r->qty, $r->revenue];
        } else {
            $rows = $this->revenueByDay($orders);
            $header = ['Tanggal', 'Jumlah Pesanan', 'Pendapatan'];
            $mapper = fn ($r) => [$r['date'], $r['count'], $r['revenue']];
        }

        return response()->streamDownload(function () use ($rows, $header, $mapper) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            foreach ($rows as $row) {
                fputcsv($out, $mapper($row));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function range(Request $request): array
    {
        $from = $request->filled('dari')
            ? Carbon::parse($request->query('dari'))->startOfDay()
            : now()->startOfMonth();
        $to = $request->filled('sampai')
            ? Carbon::parse($request->query('sampai'))->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }

    private function scopedPaidOrders(Request $request, Carbon $from, Carbon $to)
    {
        return Order::query()
            ->visibleTo($request->user())
            ->whereNotNull('paid_at')
            ->where('status', '!=', OrderStatus::PendingPayment)
            ->whereBetween('paid_at', [$from, $to])
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->integer('branch_id')));
    }

    private function revenueByDay($orders): array
    {
        return $orders
            ->groupBy(fn ($o) => $o->paid_at->toDateString())
            ->map(fn ($group, $date) => [
                'date' => $date,
                'count' => $group->count(),
                'revenue' => $group->sum('total'),
            ])
            ->sortBy('date')
            ->values()
            ->all();
    }

    private function topProducts($orderIds, ?int $limit = 10)
    {
        return OrderItem::query()
            ->whereIn('order_id', $orderIds)
            ->selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->when($limit, fn ($q) => $q->limit($limit))
            ->get();
    }

    private function statusCounts(Request $request, Carbon $from, Carbon $to): array
    {
        return Order::query()
            ->visibleTo($request->user())
            ->whereBetween('created_at', [$from, $to])
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->integer('branch_id')))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();
    }
}
