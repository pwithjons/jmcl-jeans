<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row settings table (id=1) rather than key/value, since these
     * fields are always read/written together as one shipping policy.
     */
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('inside_city_charge', 10, 2)->default(0);
            $table->decimal('outside_city_charge', 10, 2)->default(0);
            $table->boolean('free_delivery_enabled')->default(false);
            $table->decimal('free_delivery_min_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
