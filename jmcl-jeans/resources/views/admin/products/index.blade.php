@extends('admin.layouts.app')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-charcoal-900">Products</h1>
            <p class="text-charcoal-500 mt-1">Manage the JMCL JEANS catalog.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary">+ New Product</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    <form method="GET" class="bg-white border border-charcoal-100 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or SKU" class="input-field w-56">
        </div>
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">Category</label>
            <select name="category" class="input-field w-48">
                <option value="">All</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">Status</label>
            <select name="status" class="input-field w-40">
                <option value="">All</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="unpublished" {{ request('status') === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary !px-4 !py-2">Filter</button>
        @if (request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.products.index') }}" class="text-sm text-charcoal-500 hover:underline">Clear</a>
        @endif
    </form>

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Variations</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-3 flex items-center gap-3">
                            @if ($product->images->first())
                                <img src="{{ $product->images->first()->url }}" class="w-10 h-12 rounded object-cover">
                            @endif
                            <div>
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-charcoal-400">{{ $product->sku }}</div>
                            </div>
                            @if ($product->is_featured)
                                <span class="text-xs bg-gold-400/20 text-gold-500 px-2 py-0.5 rounded-full">Featured</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $product->category->name }}</td>
                        <td class="px-4 py-3">
                            @if ($product->discount_price)
                                <span class="line-through text-charcoal-400">৳{{ number_format($product->price, 2) }}</span>
                                <span class="font-medium">৳{{ number_format($product->discount_price, 2) }}</span>
                            @else
                                ৳{{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $product->variations_count }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.products.toggle-publish', $product) }}">
                                @csrf @method('patch')
                                <button type="submit"
                                        class="{{ $product->status === 'published' ? 'text-green-600' : 'text-charcoal-400' }} hover:underline">
                                    {{ ucfirst($product->status) }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-denim-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                                  onsubmit="return confirm('Delete this product?');">
                                @csrf @method('delete')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-charcoal-400">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection
