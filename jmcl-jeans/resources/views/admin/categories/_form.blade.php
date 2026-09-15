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
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Slug (optional — auto-generated from name if blank)</label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Description</label>
        <textarea name="description" rows="3" class="input-field">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Image</label>
        @if (! empty($category) && $category->image)
            <img src="{{ $category->image_url }}" class="w-20 h-20 rounded object-cover mb-2">
        @endif
        <input type="file" name="image" accept="image/*" class="input-field">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Meta Title</label>
        <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title ?? '') }}" class="input-field">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Meta Description</label>
        <textarea name="meta_description" rows="2" class="input-field">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="status" value="1" class="rounded border-charcoal-300"
                   {{ old('status', $category->status ?? true) ? 'checked' : '' }}>
            Active (visible on storefront)
        </label>
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="btn-primary">{{ isset($category) ? 'Update Category' : 'Create Category' }}</button>
    <a href="{{ route('admin.categories.index') }}" class="ml-3 text-sm text-charcoal-500 hover:underline">Cancel</a>
</div>
