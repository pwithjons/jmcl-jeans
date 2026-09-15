<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'size', 'color', 'color_hex',
        'sku', 'price_override', 'stock_quantity', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price_override' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute(): float
    {
        return (float) ($this->price_override ?? $this->product->effective_price);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }
}
