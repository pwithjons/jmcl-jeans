@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Category</label>
        <select name="category_id" required class="input-field">
            <option value="">Select a category</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $subcategory->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $subcategory->name ?? '') }}" required class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Slug (optional — auto-generated from name if blank)</label>
        <input type="text" name="slug" value="{{ old('slug', $subcategory->slug ?? '') }}" class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Description</label>
        <textarea name="description" rows="3" class="input-field">{{ old('description', $subcategory->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $subcategory->sort_order ?? 0) }}" class="input-field">
    </div>

    <div class="flex items-end">
        <label class="flex items-center gap-2 text-sm mb-2">
            <input type="checkbox" name="status" value="1" class="rounded border-charcoal-300"
                   {{ old('status', $subcategory->status ?? true) ? 'checked' : '' }}>
            Active (visible on storefront)
        </label>
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="btn-primary">{{ isset($subcategory) ? 'Update Subcategory' : 'Create Subcategory' }}</button>
    <a href="{{ route('admin.subcategories.index') }}" class="ml-3 text-sm text-charcoal-500 hover:underline">Cancel</a>
</div>
