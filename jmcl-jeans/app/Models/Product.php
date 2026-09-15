<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'subcategory_id', 'name', 'slug',
        'short_description', 'description', 'sku',
        'price', 'discount_price', 'status', 'is_featured',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    // --- Scopes ---

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('variations', fn (Builder $q) => $q->where('stock_quantity', '>', 0));
    }

    /**
     * Shared storefront filter/sort logic for Shop, Category, and Search
     * pages, so all three behave identically instead of drifting apart.
     * $filters keys: size, color, min_price, max_price, sort.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['size']) || ! empty($filters['color'])) {
            $query->whereHas('variations', function (Builder $q) use ($filters) {
                if (! empty($filters['size'])) {
                    $q->where('size', $filters['size']);
                }
                if (! empty($filters['color'])) {
                    $q->where('color', $filters['color']);
                }
            });
        }

        if (! empty($filters['min_price'])) {
            $query->where(fn (Builder $q) => $q
                ->where('discount_price', '>=', $filters['min_price'])
                ->orWhere(fn (Builder $q2) => $q2->whereNull('discount_price')->where('price', '>=', $filters['min_price'])));
        }

        if (! empty($filters['max_price'])) {
            $query->where(fn (Builder $q) => $q
                ->where('discount_price', '<=', $filters['max_price'])
                ->orWhere(fn (Builder $q2) => $q2->whereNull('discount_price')->where('price', '<=', $filters['max_price'])));
        }

        return match ($filters['sort'] ?? 'newest') {
            'price_low' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'price_high' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            'popularity' => $query->orderByDesc('view_count'),
            default => $query->orderByDesc('created_at'),
        };
    }

    // --- Derived attributes ---

    /** Price the customer actually pays right now. */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    /** Sum of stock across all variations. */
    public function getTotalStockAttribute(): int
    {
        return $this->variations->sum('stock_quantity');
    }

    public function getPrimaryImageAttribute(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
