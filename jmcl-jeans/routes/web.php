<?php

use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront routes (web guard / customers)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])
    ->middleware('throttle:5,1') // 5 submissions/minute — enough for a real visitor, not for a bot loop
    ->name('contact.submit');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');

// Cart is available to guests and logged-in customers alike (session-based
// for guests, user-based once logged in — see CartService).
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->middleware('throttle:30,1')->name('cart.store');
Route::patch('/cart/{cartId}', [CartController::class, 'update'])->middleware('throttle:30,1')->name('cart.update');
Route::delete('/cart/{cartId}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->middleware('throttle:10,1')->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout also allows guests — Cash on Delivery needs no account, only a
// delivery address (see original spec: guest checkout with COD).
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::middleware('auth:web')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/account/orders', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('/account/orders/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
