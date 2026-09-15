<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Keeps the "save a product with its variations and images" workflow out
 * of the controller. This is also the seam future features hang off of —
 * e.g. a bulk-import job or an API endpoint can reuse `save()` without
 * duplicating the sync logic.
 */
class ProductService
{
    public function create(array $data, array $variations = [], array $images = [], array $imageAltTexts = []): Product
    {
        return DB::transaction(function () use ($data, $variations, $images, $imageAltTexts) {
            $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
            $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

            $product = Product::create($data);

            $this->syncVariations($product, $variations);
            $this->addImages($product, $images, $imageAltTexts);

            return $product;
        });
    }

    public function update(
        Product $product,
        array $data,
        array $variations = [],
        array $images = [],
        array $imageAltTexts = [],
        array $deleteImageIds = [],
        ?int $primaryImageId = null,
    ): Product {
        return DB::transaction(function () use ($product, $data, $variations, $images, $imageAltTexts, $deleteImageIds, $primaryImageId) {
            $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
            $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

            $product->update($data);

            $this->syncVariations($product, $variations);
            $this->deleteImages($product, $deleteImageIds);
            $this->addImages($product, $images, $imageAltTexts);
            $this->setPrimaryImage($product, $primaryImageId);

            return $product;
        });
    }

    /**
     * Update existing variations (matched by id), create new ones (no id
     * in the submitted row), and delete any variation that used to exist
     * but was removed from the form.
     */
    private function syncVariations(Product $product, array $variations): void
    {
        $keptIds = [];

        foreach ($variations as $row) {
            // Skip completely empty rows the form may submit.
            if (empty($row['sku'])) {
                continue;
            }

            $variationId = $row['id'] ?? null;

            $attributes = [
                'size' => $row['size'] ?? null,
                'color' => $row['color'] ?? null,
                'color_hex' => $row['color_hex'] ?? null,
                'sku' => $row['sku'],
                'price_override' => $row['price_override'] ?? null,
                'stock_quantity' => $row['stock_quantity'] ?? 0,
                'status' => (bool) ($row['status'] ?? true),
            ];

            $variation = $variationId
                ? tap($product->variations()->findOrFail($variationId))->update($attributes)
                : $product->variations()->create($attributes);

            $keptIds[] = $variation->id;
        }

        $product->variations()->whereNotIn('id', $keptIds)->delete();
    }

    /** @param UploadedFile[] $images */
    private function addImages(Product $product, array $images, array $altTexts): void
    {
        if (empty($images)) {
            return;
        }

        $nextSort = (int) $product->images()->max('sort_order') + 1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = ImageOptimizer::store($file, 'products');

            $product->images()->create([
                'image_path' => $path,
                'alt_text' => $altTexts[$index] ?? $product->name,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $nextSort++,
            ]);

            $hasPrimary = true;
        }
    }

    private function deleteImages(Product $product, array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            if (! str_starts_with($image->image_path, 'http')) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // If the primary image was deleted, promote the next one so the
        // product never ends up with zero primary images while it still
        // has photos.
        if (! $product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }
    }

    private function setPrimaryImage(Product $product, ?int $primaryImageId): void
    {
        if (! $primaryImageId) {
            return;
        }

        $product->images()->update(['is_primary' => false]);
        $product->images()->where('id', $primaryImageId)->update(['is_primary' => true]);
    }
}
