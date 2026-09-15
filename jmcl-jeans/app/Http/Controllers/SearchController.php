<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $products = Product::published()
            ->with(['images', 'variations'])
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('short_description', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->filter($request->only(['size', 'color', 'min_price', 'max_price', 'sort']))
            ->paginate(12)
            ->withQueryString();

        return view('search.index', [
            'title' => 'Search results for "'.$term.'" | JMCL JEANS LTD',
            'term' => $term,
            'products' => $products,
        ]);
    }
}
