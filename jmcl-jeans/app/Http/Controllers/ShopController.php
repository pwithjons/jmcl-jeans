<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::published()
            ->with(['images', 'variations', 'category'])
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('subcategory'), fn ($q) => $q->where('subcategory_id', $request->subcategory))
            ->filter($request->only(['size', 'color', 'min_price', 'max_price', 'sort']))
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->orderBy('sort_order')->get();

        $availableColors = ProductVariation::whereNotNull('color')
            ->distinct()
            ->orderBy('color')
            ->pluck('color');

        return view('shop.index', [
            'title' => 'Shop All | JMCL JEANS LTD',
            'products' => $products,
            'categories' => $categories,
            'availableColors' => $availableColors,
        ]);
    }
}
