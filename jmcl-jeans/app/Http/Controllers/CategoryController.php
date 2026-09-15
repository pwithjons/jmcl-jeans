<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request): View
    {
        abort_unless($category->status, 404);

        $products = $category->products()
            ->published()
            ->with(['images', 'variations'])
            ->when($request->filled('subcategory'), fn ($q) => $q->where('subcategory_id', $request->subcategory))
            ->filter($request->only(['size', 'color', 'min_price', 'max_price', 'sort']))
            ->paginate(12)
            ->withQueryString();

        return view('categories.show', [
            'title' => $category->meta_title ?: $category->name.' | JMCL JEANS LTD',
            'metaDescription' => $category->meta_description,
            'category' => $category,
            'subcategories' => $category->subcategories()->active()->get(),
            'products' => $products,
        ]);
    }
}
