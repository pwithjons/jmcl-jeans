<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_order_amount',
        'usage_limit', 'used_count', 'expiry_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /** Checks status, expiry, usage limit, and minimum order amount together. */
    public function isValidFor(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->lt(Carbon::today())) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        $discount = $this->discount_type === 'percentage'
            ? $subtotal * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        // Never let a discount exceed the order subtotal.
        return min($discount, $subtotal);
    }
}
