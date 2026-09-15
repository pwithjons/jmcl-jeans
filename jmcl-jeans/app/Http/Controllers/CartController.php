<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\ProductVariation;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): View
    {
        $items = $this->cart->items();
        $subtotal = $items->sum(fn ($item) => $item->line_total);

        $appliedCoupon = null;
        $discount = 0;
        if ($code = session('applied_coupon_code')) {
            $coupon = Coupon::where('code', $code)->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $appliedCoupon = $coupon;
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon_code');
            }
        }

        return view('cart.index', [
            'title' => 'Your Cart | JMCL JEANS LTD',
            'items' => $items,
            'subtotal' => $subtotal,
            'appliedCoupon' => $appliedCoupon,
            'discount' => $discount,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variation_id' => ['required', 'exists:product_variations,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $variation = ProductVariation::findOrFail($validated['product_variation_id']);

        if ($variation->stock_quantity < 1) {
            return back()->with('error', 'Sorry, that size/color combination is out of stock.');
        }

        $this->cart->add($validated['product_id'], $variation->id, $validated['quantity']);

        return redirect()->route('cart.index')->with('status', 'Added to cart.');
    }

    public function update(Request $request, int $cartId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $this->cart->updateQuantity($cartId, $validated['quantity']);

        return redirect()->route('cart.index')->with('status', 'Cart updated.');
    }

    public function destroy(int $cartId): RedirectResponse
    {
        $this->cart->remove($cartId);

        return redirect()->route('cart.index')->with('status', 'Item removed.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'string']]);

        $subtotal = $this->cart->subtotal();
        $coupon = Coupon::where('code', strtoupper($validated['code']))->first();

        if (! $coupon || ! $coupon->isValidFor($subtotal)) {
            return back()->with('error', 'That coupon code is invalid or does not apply to your order.');
        }

        session(['applied_coupon_code' => $coupon->code]);

        return redirect()->route('cart.index')->with('status', 'Coupon applied.');
    }

    public function removeCoupon(): RedirectResponse
    {
        session()->forget('applied_coupon_code');

        return redirect()->route('cart.index')->with('status', 'Coupon removed.');
    }
}
