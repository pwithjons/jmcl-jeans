<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 1000,
                'usage_limit' => null,
                'expiry_date' => now()->addMonths(6),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'FLAT200'],
            [
                'discount_type' => 'fixed',
                'discount_value' => 200,
                'min_order_amount' => 2000,
                'usage_limit' => 100,
                'expiry_date' => now()->addMonths(3),
                'is_active' => true,
            ]
        );
    }
}
