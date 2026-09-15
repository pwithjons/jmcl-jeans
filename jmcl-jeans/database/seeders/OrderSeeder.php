<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    /**
     * Demo-only data so the Phase 3b admin Orders/Customers screens have
     * something to show without needing the storefront checkout (Phase 4)
     * to exist yet.
     */
    public function run(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'demo.customer@jmcljeans.test'],
            [
                'name' => 'Rahim Ahmed',
                'phone' => '+8801711000000',
                'password' => Hash::make('password'),
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $customer->id, 'address_line' => 'House 12, Road 5, Dhanmondi'],
            [
                'full_name' => 'Rahim Ahmed',
                'phone' => '+8801711000000',
                'city' => 'Dhaka',
                'delivery_area' => 'Inside City',
                'is_default' => true,
            ]
        );

        $products = Product::with('variations')->published()->get();

        if ($products->isEmpty()) {
            return; // ProductSeeder hasn't run — nothing to build demo orders from.
        }

        $demoOrders = [
            ['status' => 'delivered', 'payment_status' => 'paid'],
            ['status' => 'processing', 'payment_status' => 'pending'],
            ['status' => 'pending', 'payment_status' => 'pending'],
        ];

        foreach ($demoOrders as $index => $meta) {
            $product = $products[$index % $products->count()];
            $variation = $product->variations->first();
            $quantity = $index + 1;
            $unitPrice = $variation?->price ?? $product->effective_price;
            $subtotal = $unitPrice * $quantity;
            $deliveryCharge = 60;
            $grandTotal = $subtotal + $deliveryCharge;

            $order = Order::updateOrCreate(
                ['order_number' => 'JMCL-DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $customer->id,
                    'full_name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => 'House 12, Road 5, Dhanmondi',
                    'city' => 'Dhaka',
                    'delivery_area' => 'Inside City',
                    'order_notes' => null,
                    'subtotal' => $subtotal,
                    'delivery_charge' => $deliveryCharge,
                    'discount' => 0,
                    'grand_total' => $grandTotal,
                    'payment_method' => 'cash_on_delivery',
                    'payment_status' => $meta['payment_status'],
                    'order_status' => $meta['status'],
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $product->id],
                [
                    'product_variation_id' => $variation?->id,
                    'product_name' => $product->name,
                    'size' => $variation?->size,
                    'color' => $variation?->color,
                    'sku' => $variation?->sku ?? $product->sku,
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]
            );
        }
    }
}
