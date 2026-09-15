<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'inside_city_charge', 'outside_city_charge',
        'free_delivery_enabled', 'free_delivery_min_amount',
    ];

    protected function casts(): array
    {
        return [
            'inside_city_charge' => 'decimal:2',
            'outside_city_charge' => 'decimal:2',
            'free_delivery_enabled' => 'boolean',
            'free_delivery_min_amount' => 'decimal:2',
        ];
    }

    /** There is only ever one row; create it with defaults on first access. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'inside_city_charge' => 60,
            'outside_city_charge' => 120,
            'free_delivery_enabled' => false,
        ]);
    }

    public function calculateCharge(float $subtotal, bool $isInsideCity): float
    {
        if ($this->free_delivery_enabled
            && $this->free_delivery_min_amount !== null
            && $subtotal >= (float) $this->free_delivery_min_amount) {
            return 0.0;
        }

        return (float) ($isInsideCity ? $this->inside_city_charge : $this->outside_city_charge);
    }
}
