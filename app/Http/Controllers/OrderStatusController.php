<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;

class OrderStatusController extends Controller
{
    /**
     * Durable guest order-status page. Bound by public_reference (UUID), never
     * the sequential id — no login, just the unguessable token in the URL.
     */
    public function show(Order $order)
    {
        return view('orders.show', [
            'setting' => Setting::current(),
            'order' => $order->load('items', 'branch'),
        ]);
    }
}
