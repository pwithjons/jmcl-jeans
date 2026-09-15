<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product name/size/color/price are snapshotted here so an order
     * always reflects what the customer actually bought, even if the
     * product is later edited, discontinued, or soft-deleted.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained()->nullOnDelete();

            $table->string('product_name');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2); // unit price at time of purchase
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
