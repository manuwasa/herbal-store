<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'setting' => Setting::current(),
            'totalProducts' => Product::query()->count(),
            'activeProducts' => Product::query()->active()->count(),
            'totalCategories' => Category::query()->count(),
        ]);
    }
}
