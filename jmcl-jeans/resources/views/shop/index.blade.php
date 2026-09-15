@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="section-heading mb-8">Shop All</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        {{-- Filters --}}
        <aside class="md:col-span-1">
            <form method="GET" class="space-y-6 sticky top-24">
                @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                <div>
                    <h3 class="text-sm font-semibold mb-2">Category</h3>
                    <select name="category" class="input-field text-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <h3 class="text-sm font-semibold mb-2">Size</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="{{ $size }}" class="peer hidden"
                                       {{ request('size') === $size ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="block px-3 py-1.5 text-sm border border-charcoal-200 rounded peer-checked:bg-charcoal-900 peer-checked:text-white peer-checked:border-charcoal-900">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold mb-2">Color</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($availableColors as $color)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $color }}" class="peer hidden"
                                       {{ request('color') === $color ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="block px-3 py-1.5 text-sm border border-charcoal-200 rounded peer-checked:bg-charcoal-900 peer-checked:text-white peer-checked:border-charcoal-900">{{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold mb-2">Price Range (৳)</h3>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="input-field text-sm">
                        <span class="text-charcoal-400">–</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="input-field text-sm">
                    </div>
                    <button type="submit" class="btn-secondary !px-3 !py-1.5 text-xs mt-2 w-full">Apply</button>
                </div>

                @if (request()->hasAny(['category', 'size', 'color', 'min_price', 'max_price']))
                    <a href="{{ route('shop.index') }}" class="text-xs text-charcoal-500 hover:underline block">Clear all filters</a>
                @endif
            </form>
        </aside>

        {{-- Products --}}
        <div class="md:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-charcoal-500">{{ $products->total() }} products</p>
                <form method="GET">
                    @foreach (request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="input-field text-sm !py-1.5">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="popularity" {{ request('sort') === 'popularity' ? 'selected' : '' }}>Popularity</option>
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="text-center py-20 text-charcoal-400">
                    No products match these filters. Try clearing a filter above.
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-8">
                    @foreach ($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
