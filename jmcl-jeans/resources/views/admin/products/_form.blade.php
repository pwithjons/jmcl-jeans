@php
    $isEdit = isset($product);
    $categoriesJson = $categories->map(fn ($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'subcategories' => $c->subcategories->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values(),
    ])->values();

    $initialVariations = $isEdit
        ? $product->variations->map(fn ($v) => [
            'id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'color_hex' => $v->color_hex,
            'sku' => $v->sku, 'price_override' => $v->price_override, 'stock_quantity' => $v->stock_quantity,
            'status' => (bool) $v->status,
        ])->values()
        : collect();
@endphp

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="productForm({
        categories: {{ $categoriesJson->toJson() }},
        selectedCategory: {{ (int) old('category_id', $product->category_id ?? 0) }},
        selectedSubcategory: {{ (int) old('subcategory_id', $product->subcategory_id ?? 0) }},
        variations: {{ $initialVariations->toJson() }}
    })" x-init="init()">

    {{-- Basic info --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6 space-y-4">
        <h2 class="font-semibold text-lg">Basic Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Product Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Category</label>
                <select name="category_id" x-model.number="selectedCategory" @change="selectedSubcategory = 0" required class="input-field">
                    <option value="0">Select a category</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name" :selected="cat.id === selectedCategory"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Subcategory</label>
                <select name="subcategory_id" x-model.number="selectedSubcategory" class="input-field">
                    <option value="0">None</option>
                    <template x-for="sub in currentSubcategories" :key="sub.id">
                        <option :value="sub.id" x-text="sub.name" :selected="sub.id === selectedSubcategory"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Slug (optional)</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Discount Price (optional)</label>
                <input type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price', $product->discount_price ?? '') }}" class="input-field">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Short Description</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" class="input-field">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Full Description</label>
                <textarea name="description" rows="4" class="input-field">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Status</label>
                <select name="status" class="input-field">
                    <option value="unpublished" {{ old('status', $product->status ?? 'unpublished') === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                    <option value="published" {{ old('status', $product->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-charcoal-300"
                           {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                    Featured product
                </label>
            </div>
        </div>
    </div>

    {{-- SEO --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6 space-y-4 mt-6">
        <h2 class="font-semibold text-lg">SEO</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description', $product->meta_description ?? '') }}" class="input-field">
            </div>
        </div>
    </div>

    {{-- Variations --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Sizes, Colors & Stock</h2>
            <button type="button" @click="addVariation()" class="btn-secondary !px-4 !py-2">+ Add Variation</button>
        </div>

        <template x-if="variations.length === 0">
            <p class="text-sm text-charcoal-400">No variations yet — add at least one size/color combination so this product can be sold.</p>
        </template>

        <div class="space-y-3">
            <template x-for="(variation, index) in variations" :key="index">
                <div class="grid grid-cols-2 md:grid-cols-7 gap-2 items-center border border-charcoal-100 rounded-md p-3">
                    <input type="hidden" :name="`variations[${index}][id]`" x-model="variation.id">

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Size</label>
                        <select :name="`variations[${index}][size]`" x-model="variation.size" class="input-field !py-1.5 text-sm">
                            <option value="">-</option>
                            <option>S</option><option>M</option><option>L</option><option>XL</option><option>XXL</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Color</label>
                        <input type="text" :name="`variations[${index}][color]`" x-model="variation.color" placeholder="Blue" class="input-field !py-1.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Color Hex</label>
                        <input type="text" :name="`variations[${index}][color_hex]`" x-model="variation.color_hex" placeholder="#1a2e4a" class="input-field !py-1.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Variation SKU</label>
                        <input type="text" :name="`variations[${index}][sku]`" x-model="variation.sku" required class="input-field !py-1.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Price Override</label>
                        <input type="number" step="0.01" :name="`variations[${index}][price_override]`" x-model="variation.price_override" class="input-field !py-1.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-charcoal-500 mb-1">Stock Qty</label>
                        <input type="number" min="0" :name="`variations[${index}][stock_quantity]`" x-model="variation.stock_quantity" required class="input-field !py-1.5 text-sm">
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" :name="`variations[${index}][status]`" value="1" x-model="variation.status" class="rounded border-charcoal-300">
                            Active
                        </label>
                        <button type="button" @click="removeVariation(index)" class="text-red-600 text-xs hover:underline">Remove</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Images --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6 mt-6">
        <h2 class="font-semibold text-lg mb-4">Product Images</h2>

        @if ($isEdit && $product->images->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                @foreach ($product->images as $image)
                    <div class="relative border border-charcoal-100 rounded-md p-2 text-center">
                        <img src="{{ $image->url }}" class="w-full h-24 object-cover rounded mb-2">
                        <label class="flex items-center justify-center gap-1 text-xs mb-1">
                            <input type="radio" name="primary_image_id" value="{{ $image->id }}" {{ $image->is_primary ? 'checked' : '' }}>
                            Primary
                        </label>
                        <label class="flex items-center justify-center gap-1 text-xs text-red-600">
                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                            Delete
                        </label>
                    </div>
                @endforeach
            </div>
        @endif

        <label class="block text-sm font-medium text-charcoal-700 mb-1">
            {{ $isEdit ? 'Add More Images' : 'Upload Images' }}
        </label>
        <input type="file" name="images[]" multiple accept="image/*" class="input-field">
        <p class="text-xs text-charcoal-400 mt-1">The first uploaded image becomes primary if the product has none yet.</p>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Update Product' : 'Create Product' }}</button>
        <a href="{{ route('admin.products.index') }}" class="ml-3 text-sm text-charcoal-500 hover:underline">Cancel</a>
    </div>
</div>

@once
    @push('scripts')
    <script>
        function productForm({ categories, selectedCategory, selectedSubcategory, variations }) {
            return {
                categories,
                selectedCategory,
                selectedSubcategory,
                variations,
                get currentSubcategories() {
                    const cat = this.categories.find(c => c.id === this.selectedCategory);
                    return cat ? cat.subcategories : [];
                },
                addVariation() {
                    this.variations.push({
                        id: null, size: '', color: '', color_hex: '',
                        sku: '', price_override: '', stock_quantity: 0, status: true,
                    });
                },
                removeVariation(index) {
                    this.variations.splice(index, 1);
                },
                init() {},
            };
        }
    </script>
    @endpush
@endonce
