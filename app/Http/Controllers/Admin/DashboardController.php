<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.dashboard', [
            'setting' => Setting::current(),
            'totalProducts' => Product::query()->count(),
            'activeProducts' => Product::query()->active()->count(),
            'totalCategories' => Category::query()->count(),
            // Paid-but-not-yet-processing = "needs your attention". Role-scoped:
            // owner sees all branches, staff see only their own.
            'newOrderCount' => Order::query()
                ->visibleTo($request->user())
                ->where('status', OrderStatus::Paid)
                ->count(),
        ]);
    }
}
