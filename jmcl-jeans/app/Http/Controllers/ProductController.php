<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->status === 'published', 404);

        $product->load(['images', 'variations', 'category', 'approvedReviews.user']);
        $product->increment('view_count');

        $relatedProducts = Product::published()
            ->with(['images', 'variations'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $sizes = $product->variations->pluck('size')->filter()->unique()->values();
        $colors = $product->variations->pluck('color')->filter()->unique()->values();

        return view('products.show', [
            'title' => ($product->meta_title ?: $product->name).' | JMCL JEANS LTD',
            'metaDescription' => $product->meta_description ?: $product->short_description,
            'ogImage' => $product->primary_image?->url,
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'sizes' => $sizes,
            'colors' => $colors,
        ]);
    }
}
