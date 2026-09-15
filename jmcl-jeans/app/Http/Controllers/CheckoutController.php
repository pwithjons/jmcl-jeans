<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $items->sum(fn ($item) => $item->line_total);

        $discount = 0;
        $appliedCoupon = null;
        if ($code = session('applied_coupon_code')) {
            $coupon = Coupon::where('code', $code)->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $appliedCoupon = $coupon;
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $defaultAddress = Auth::guard('web')->check() ? Auth::guard('web')->user()->defaultAddress() : null;

        return view('checkout.index', [
            'title' => 'Checkout | JMCL JEANS LTD',
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'appliedCoupon' => $appliedCoupon,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'delivery_area' => ['nullable', 'string', 'max:100'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'delivery_zone' => ['required', 'in:inside,outside'],
        ]);

        try {
            $order = $this->checkout->placeOrder($validated, session('applied_coupon_code'));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        session()->forget('applied_coupon_code');

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.confirmation', [
            'title' => 'Order Confirmed | JMCL JEANS LTD',
            'order' => $order,
        ]);
    }
}
