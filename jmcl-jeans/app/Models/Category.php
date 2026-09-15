<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image',
        'meta_title', 'meta_description', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        // Keeps the nav-bar cache (AppServiceProvider) and sitemap cache
        // (SitemapController) from ever showing a stale category list —
        // cheaper than remembering to clear it in every controller
        // action that touches a category.
        static::saved(fn () => static::forgetSharedCaches());
        static::deleted(fn () => static::forgetSharedCaches());
    }

    private static function forgetSharedCaches(): void
    {
        \Illuminate\Support\Facades\Cache::forget('nav.categories');
        \Illuminate\Support\Facades\Cache::forget('sitemap.xml');
    }
}
