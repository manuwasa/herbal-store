<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'setting' => Setting::current(),
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'featuredProducts' => Product::query()->active()->with('category')->latest()->take(8)->get(),
        ]);
    }
}
