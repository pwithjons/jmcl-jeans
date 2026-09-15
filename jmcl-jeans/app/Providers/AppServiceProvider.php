<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Most no-SSH shared hosts (see DEPLOY.md) sit behind a proxy that
        // terminates HTTPS, so Laravel would otherwise generate http://
        // links for assets/redirects. Forcing https:// in production
        // avoids mixed-content warnings without needing TrustProxies
        // configuration most shared hosts won't let you touch anyway.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Only a Super Admin can manage other admin accounts, settings,
        // and role assignments. Regular Admins get routine e-commerce
        // management (products, orders, categories, coupons, shipping)
        // via the `role:admin,super_admin` route middleware instead —
        // gates here are for the handful of actions that must stay
        // Super-Admin-exclusive regardless of route.
        Gate::define('manage-admins', fn (Admin $admin) => $admin->isSuperAdmin());
        Gate::define('manage-settings', fn (Admin $admin) => $admin->isSuperAdmin());

        // Shared with every storefront page (main nav + footer). Cached
        // briefly since this composer runs on literally every storefront
        // request — without it, every page load would run this query
        // just to render a nav bar that rarely changes.
        View::composer('layouts.app', function ($view) {
            $view->with('navCategories', Cache::remember('nav.categories', 300, function () {
                return Category::active()->orderBy('sort_order')->limit(6)->get();
            }));

            $view->with('cartCount', app(CartService::class)->totalQuantity());
        });
    }
}
