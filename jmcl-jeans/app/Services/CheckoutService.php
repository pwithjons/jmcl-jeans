<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(private readonly CartService $cart)
    {
    }

    /**
     * @param  array{full_name:string,phone:string,email:?string,address:string,city:string,delivery_area:?string,order_notes:?string,delivery_zone:string}  $customer
     *
     * @throws RuntimeException when the cart is empty or an item's stock
     *                           changed since it was added (message is
     *                           safe to show the customer directly)
     */
    public function placeOrder(array $customer, ?string $couponCode = null): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new RuntimeException('Your cart is empty.');
        }

        return DB::transaction(function () use ($items, $customer, $couponCode) {
            // Re-check stock at the moment of purchase — the cart page
            // clamps quantities when items are added, but stock can move
            // between then and checkout (another order, an admin edit).
            foreach ($items as $item) {
                $available = $item->variation?->stock_quantity ?? 0;
                if ($item->quantity > $available) {
                    $variantLabel = $item->variation ? "({$item->variation->size}/{$item->variation->color})" : '';
                    throw new RuntimeException(
                        "Sorry, only {$available} left of {$item->product->name} {$variantLabel}. Please update your cart."
                    );
                }
            }

            $subtotal = $items->sum(fn ($item) => $item->line_total);

            $coupon = null;
            $discount = 0.0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValidFor($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                } else {
                    $coupon = null;
                }
            }

            $shipping = ShippingSetting::current();
            $isInsideCity = $customer['delivery_zone'] === 'inside';
            $deliveryCharge = $shipping->calculateCharge($subtotal - $discount, $isInsideCity);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::guard('web')->id(),
                'full_name' => $customer['full_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'] ?? null,
                'address' => $customer['address'],
                'city' => $customer['city'],
                'delivery_area' => $customer['delivery_area'] ?? null,
                'order_notes' => $customer['order_notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'discount' => $discount,
                'grand_total' => $subtotal - $discount + $deliveryCharge,
                'coupon_id' => $coupon?->id,
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variation_id' => $item->product_variation_id,
                    'product_name' => $item->product->name,
                    'size' => $item->variation?->size,
                    'color' => $item->variation?->color,
                    'sku' => $item->variation?->sku ?? $item->product->sku,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->line_total,
                ]);

                $item->variation?->decrement('stock_quantity', $item->quantity);
            }

            $coupon?->increment('used_count');

            $this->cart->clear();

            return $order;
        });
    }
}
