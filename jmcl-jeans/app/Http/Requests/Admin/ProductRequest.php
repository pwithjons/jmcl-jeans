<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'status' => ['required', Rule::in(['published', 'unpublished'])],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],

            // Variations (size/color combinations)
            'variations' => ['nullable', 'array'],
            'variations.*.id' => ['nullable', 'integer', 'exists:product_variations,id'],
            'variations.*.size' => ['nullable', 'string', 'max:20'],
            'variations.*.color' => ['nullable', 'string', 'max:50'],
            'variations.*.color_hex' => ['nullable', 'string', 'max:7'],
            'variations.*.sku' => ['required_with:variations', 'string', 'max:150'],
            'variations.*.price_override' => ['nullable', 'numeric', 'min:0'],
            'variations.*.stock_quantity' => ['required_with:variations', 'integer', 'min:0'],
            'variations.*.status' => ['nullable', 'boolean'],

            // New image uploads
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'image_alt_texts' => ['nullable', 'array'],
            'image_alt_texts.*' => ['nullable', 'string', 'max:255'],

            // Existing image management
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:product_images,id'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ];
    }
}
