@extends('layouts.app')

@section('content')
@php
    $variationsJson = $product->variations->map(fn ($v) => [
        'id' => $v->id, 'size' => $v->size, 'color' => $v->color,
        'price' => (float) $v->price, 'stock' => $v->stock_quantity,
    ])->values();
@endphp

<div class="container mx-auto px-4 py-10" x-data="productPage({
        variations: {{ $variationsJson->toJson() }},
        basePrice: {{ (float) $product->effective_price }},
        sizes: {{ $sizes->toJson() }},
        colors: {{ $colors->toJson() }}
    })" x-init="init()">

    <nav class="text-xs text-charcoal-400 mb-6">
        <a href="{{ route('shop.index') }}" class="hover:text-charcoal-600">Shop</a> /
        <a href="{{ route('categories.show', $product->category) }}" class="hover:text-charcoal-600">{{ $product->category->name }}</a> /
        <span class="text-charcoal-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        {{-- Gallery --}}
        <div x-data="{ active: 0 }">
            <div class="aspect-[4/5] bg-charcoal-50 overflow-hidden">
                @foreach ($product->images as $image)
                    <img x-show="active === {{ $loop->index }}" src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}" class="w-full h-full object-cover">
                @endforeach
            </div>
            @if ($product->images->count() > 1)
                <div class="flex gap-2 mt-3">
                    @foreach ($product->images as $image)
                        <button @click="active = {{ $loop->index }}"
                                :class="active === {{ $loop->index }} ? 'border-charcoal-900' : 'border-transparent'"
                                class="w-16 h-20 border-2 overflow-hidden shrink-0">
                            <img src="{{ $image->url }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <h1 class="font-display text-3xl font-bold text-charcoal-900">{{ $product->name }}</h1>

            @if ($product->average_rating > 0)
                <div class="flex items-center gap-1 mt-2 text-sm text-charcoal-500">
                    <span class="text-gold-500">&#9733;</span> {{ $product->average_rating }}
                    ({{ $product->approvedReviews()->count() }} reviews)
                </div>
            @endif

            <div class="mt-4 text-2xl font-semibold text-charcoal-900">
                ৳<span x-text="displayPrice.toFixed(2)"></span>
                @if ($product->is_on_sale)
                    <span class="text-base text-charcoal-400 line-through ml-2">৳{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if ($product->short_description)
                <p class="text-charcoal-500 mt-4">{{ $product->short_description }}</p>
            @endif

            @if ($sizes->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-semibold mb-2">Size</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($sizes as $size)
                            <button @click="selectedSize = '{{ $size }}'"
                                    :class="selectedSize === '{{ $size }}' ? 'bg-charcoal-900 text-white border-charcoal-900' : 'border-charcoal-200'"
                                    class="px-4 py-2 text-sm border rounded">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($colors->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-sm font-semibold mb-2">Color</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($colors as $color)
                            <button @click="selectedColor = '{{ $color }}'"
                                    :class="selectedColor === '{{ $color }}' ? 'bg-charcoal-900 text-white border-charcoal-900' : 'border-charcoal-200'"
                                    class="px-4 py-2 text-sm border rounded">
                                {{ $color }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-4 text-sm" x-show="selectedVariation">
                <span x-show="selectedVariation && selectedVariation.stock > 0" class="text-green-600">In stock (<span x-text="selectedVariation ? selectedVariation.stock : ''"></span> left)</span>
                <span x-show="selectedVariation && selectedVariation.stock === 0" class="text-red-600">Out of stock in this combination</span>
            </div>
            <p class="mt-2 text-xs text-charcoal-400" x-show="!selectedVariation">Select a size and color to continue.</p>

            <form method="POST" action="{{ route('cart.store') }}" class="mt-6 flex items-center gap-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variation_id" :value="selectedVariation ? selectedVariation.id : ''">
                <input type="hidden" name="quantity" :value="quantity">

                <div class="flex items-center border border-charcoal-200 rounded">
                    <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 text-charcoal-500">-</button>
                    <span class="px-4 text-sm" x-text="quantity"></span>
                    <button type="button" @click="quantity++" class="px-3 py-2 text-charcoal-500">+</button>
                </div>

                <button type="submit"
                        :disabled="!selectedVariation || selectedVariation.stock === 0"
                        :class="(!selectedVariation || selectedVariation.stock === 0) ? 'opacity-50 cursor-not-allowed' : ''"
                        class="btn-primary flex-1">
                    Add to Cart
                </button>
            </form>

            <div class="mt-10 border-t border-charcoal-100 pt-6">
                <h3 class="font-semibold mb-2">Description</h3>
                <p class="text-sm text-charcoal-600 whitespace-pre-line">{{ $product->description }}</p>
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    @if ($product->approvedReviews->isNotEmpty())
    <div class="mt-16 max-w-2xl">
        <h2 class="section-heading mb-6">Customer Reviews</h2>
        <div class="space-y-6">
            @foreach ($product->approvedReviews as $review)
                <div class="border-b border-charcoal-100 pb-4">
                    <div class="text-gold-500 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                    <p class="text-sm text-charcoal-600 mt-1">{{ $review->comment }}</p>
                    <p class="text-xs text-charcoal-400 mt-1">{{ $review->user->name }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Related products --}}
    @if ($relatedProducts->isNotEmpty())
    <div class="mt-16">
        <h2 class="section-heading mb-6">You May Also Like</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
            @foreach ($relatedProducts as $related)
                @include('partials.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
    @endif
</div>

@once
    @push('scripts')
    <script>
        function productPage({ variations, basePrice, sizes, colors }) {
            return {
                variations,
                basePrice,
                selectedSize: sizes.length === 1 ? sizes[0] : null,
                selectedColor: colors.length === 1 ? colors[0] : null,
                quantity: 1,
                get selectedVariation() {
                    return this.variations.find(v =>
                        (this.selectedSize ? v.size === this.selectedSize : true) &&
                        (this.selectedColor ? v.color === this.selectedColor : true)
                    ) || null;
                },
                get displayPrice() {
                    return this.selectedVariation ? this.selectedVariation.price : this.basePrice;
                },
                init() {},
            };
        }
    </script>
    @endpush
@endonce
@endsection
