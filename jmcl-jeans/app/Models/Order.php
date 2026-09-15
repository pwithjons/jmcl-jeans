<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'full_name', 'phone', 'email',
        'address', 'city', 'delivery_area', 'order_notes',
        'subtotal', 'delivery_charge', 'discount', 'grand_total',
        'coupon_id', 'payment_method', 'payment_status', 'order_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'discount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('order_status', $status);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('order_number', 'like', "%{$term}%")
                ->orWhere('full_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    /** Generates a human-readable, sortable order number, e.g. JMCL-20260913-0001. */
    public static function generateOrderNumber(): string
    {
        $today = now()->format('Ymd');
        $countToday = static::whereDate('created_at', now())->count() + 1;

        return sprintf('JMCL-%s-%04d', $today, $countToday);
    }
}
