<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `payment_method` is a plain string (not an enum) on purpose: adding
     * bKash/Nagad/SSLCommerz later means registering a new
     * PaymentGatewayInterface implementation, not a migration. Customer
     * contact fields are duplicated onto the order itself (not just
     * user_id) so an order remains a complete historical record even if
     * the customer later edits their profile, or the order was placed as
     * a guest.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = guest checkout

            // Snapshot of customer/delivery info at time of order
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('delivery_area')->nullable();
            $table->text('order_notes')->nullable();

            // Totals
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // Payment — string on purpose, see class docblock
            $table->string('payment_method')->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->enum('order_status', [
                'pending', 'processing', 'shipped', 'delivered', 'cancelled',
            ])->default('pending');

            $table->timestamps();

            $table->index('order_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
