<?php

namespace Database\Seeders;

use App\Models\ShippingSetting;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    public function run(): void
    {
        ShippingSetting::updateOrCreate(['id' => 1], [
            'inside_city_charge' => 60,
            'outside_city_charge' => 120,
            'free_delivery_enabled' => true,
            'free_delivery_min_amount' => 3000,
        ]);
    }
}
