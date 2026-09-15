<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingSetting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::published()->featured()
            ->with(['images', 'variations'])
            ->limit(4)->get();

        $newArrivals = Product::published()
            ->with(['images', 'variations'])
            ->orderByDesc('created_at')
            ->limit(8)->get();

        $popularProducts = Product::published()
            ->with(['images', 'variations'])
            ->orderByDesc('view_count')
            ->limit(4)->get();

        $categories = Category::active()->orderBy('sort_order')->limit(5)->get();

        $shipping = ShippingSetting::current();

        return view('home', [
            'title' => Setting::get('store_name', 'JMCL JEANS LTD').' — Premium Denim',
            'metaDescription' => Setting::get('homepage_hero_subtitle') ?: 'Premium denim and modern fits from JMCL JEANS LTD. Cash on delivery nationwide.',
            'featuredProducts' => $featuredProducts,
            'newArrivals' => $newArrivals,
            'popularProducts' => $popularProducts,
            'categories' => $categories,
            'shipping' => $shipping,
            'heroTitle' => Setting::get('homepage_hero_title', 'Premium Denim. Modern Fit.'),
            'heroSubtitle' => Setting::get('homepage_hero_subtitle', ''),
        ]);
    }
}
