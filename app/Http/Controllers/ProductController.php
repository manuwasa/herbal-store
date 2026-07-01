<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->active()
            ->with('category')
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->query('category')));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->query('search') . '%');
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', [
            'setting' => Setting::current(),
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'activeCategory' => $request->query('category'),
            'search' => $request->query('search'),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('catalog.show', [
            'setting' => Setting::current(),
            'product' => $product->load('category'),
        ]);
    }
}
