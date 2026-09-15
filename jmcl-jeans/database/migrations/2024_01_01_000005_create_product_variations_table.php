<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per (size, color) combination of a product. Stock is tracked
     * per-variation so "Blue / M" can sell out while "Blue / L" is still
     * available. `price_override` lets a specific variation (e.g. XXL)
     * cost more without touching the base product price.
     */
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size')->nullable();   // S, M, L, XL, XXL
            $table->string('color')->nullable();  // Black, Blue, Navy, Grey...
            $table->string('color_hex', 7)->nullable(); // for swatch UI, e.g. #1a2e4a
            $table->string('sku')->unique();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'size', 'color'], 'unique_variation_per_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
