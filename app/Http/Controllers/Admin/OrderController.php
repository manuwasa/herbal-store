<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->visibleTo($request->user())
            ->with('branch')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->integer('branch_id')))
            ->latest()
            ->get();

        return view('admin.orders.index', [
            'orders' => $orders,
            'branches' => Branch::query()->orderBy('name')->get(),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items',
            'branch',
            'paymentTransactions' => fn ($q) => $q->latest('id'),
            'statusHistories.changedBy',
            'shippingRateLookup',
        ]);

        return view('admin.orders.show', [
            'order' => $order,
            'branches' => Branch::query()->orderBy('name')->get(),
        ]);
    }

    public function invoice(Order $order)
    {
        $this->authorize('view', $order);

        return view('admin.orders.invoice', [
            'order' => $order->load('items', 'branch', 'latestPaymentTransaction'),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'action' => ['required', Rule::in(['processing', 'shipped', 'completed', 'cancelled'])],
            'courier_name' => ['required_if:action,shipped', 'nullable', 'string', 'max:100'],
            'tracking_number' => ['required_if:action,shipped', 'nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            match ($data['action']) {
                'processing' => $order->markAsProcessing($request->user()),
                'shipped' => $order->markAsShipped($request->user(), $data['courier_name'], $data['tracking_number']),
                'completed' => $order->markAsCompleted($request->user()),
                'cancelled' => $order->markAsCancelled($request->user(), $data['note'] ?? 'Dibatalkan admin'),
            };
        } catch (InvalidOrderTransitionException $e) {
            return back()->withErrors(['action' => $e->getMessage()]);
        }

        return redirect()->route('admin.pesanan.show', $order)->with('status', 'Status pesanan berhasil diperbarui.');
    }

    public function reassignBranch(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        try {
            $order->reassignBranch($request->user(), Branch::findOrFail($data['branch_id']));
        } catch (InvalidOrderTransitionException $e) {
            return back()->withErrors(['branch_id' => $e->getMessage()]);
        }

        return redirect()->route('admin.pesanan.show', $order)->with('status', 'Cabang pemenuhan pesanan berhasil dipindahkan.');
    }
}
