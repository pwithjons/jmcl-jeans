<?php

use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\SubcategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel routes (admin guard — completely separate session from
| the storefront's `web` guard, see config/auth.php)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Available to both roles — routine e-commerce management.
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Routine e-commerce management — both Admin and Super Admin.
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('subcategories', SubcategoryController::class)->except(['show']);

            Route::resource('products', ProductController::class)->except(['show']);
            Route::patch('products/{product}/toggle-publish', [ProductController::class, 'togglePublish'])
                ->name('products.toggle-publish');
            Route::get('categories/{category}/subcategories-for', [ProductController::class, 'subcategoriesFor'])
                ->name('categories.subcategories-for');

            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

            Route::resource('coupons', CouponController::class)->except(['show']);

            Route::get('shipping', [ShippingController::class, 'edit'])->name('shipping.edit');
            Route::put('shipping', [ShippingController::class, 'update'])->name('shipping.update');

            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        });

        // Super Admin only.
        Route::middleware('role:super_admin')->group(function () {
            Route::get('admins', [AdminManagementController::class, 'index'])->name('admins.index');
        });
    });
});
