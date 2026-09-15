<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Cached for an hour — a sitemap doesn't need to be byte-fresh, and
     * without caching this would run two full-table queries on every
     * crawler hit.
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $urls = collect();

            $urls->push(['loc' => route('home'), 'priority' => '1.0']);
            $urls->push(['loc' => route('shop.index'), 'priority' => '0.9']);

            Category::active()->get()->each(function (Category $category) use ($urls) {
                $urls->push(['loc' => route('categories.show', $category), 'priority' => '0.7']);
            });

            Product::published()->get(['slug', 'updated_at'])->each(function (Product $product) use ($urls) {
                $urls->push([
                    'loc' => route('products.show', $product),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'priority' => '0.8',
                ]);
            });

            foreach (['about', 'contact', 'privacy', 'terms'] as $routeName) {
                $urls->push(['loc' => route($routeName), 'priority' => '0.3']);
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
            foreach ($urls as $url) {
                $xml .= '  <url>'."\n";
                $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
                if (isset($url['lastmod'])) {
                    $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
                }
                $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";
                $xml .= '  </url>'."\n";
            }
            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n".
            "Disallow: /admin\n".
            "Disallow: /cart\n".
            "Disallow: /checkout\n".
            "Disallow: /account\n".
            "Disallow: /profile\n".
            "Allow: /\n\n".
            'Sitemap: '.route('sitemap')."\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
