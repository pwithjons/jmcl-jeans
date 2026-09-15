{{-- Expects: $product (with images and variations loaded/eager-loadable) --}}
<a href="{{ route('products.show', $product) }}" class="group block">
    <div class="relative aspect-[4/5] bg-charcoal-50 overflow-hidden">
        @if ($product->primary_image)
            <img src="{{ $product->primary_image->url }}" alt="{{ $product->primary_image->alt_text ?? $product->name }}"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        @endif

        @if ($product->is_on_sale)
            <span class="absolute top-3 left-3 bg-charcoal-900 text-white text-xs px-2.5 py-1">Sale</span>
        @elseif ($product->is_featured)
            <span class="absolute top-3 left-3 bg-gold-500 text-white text-xs px-2.5 py-1">Featured</span>
        @endif

        @if ($product->total_stock === 0)
            <span class="absolute inset-0 bg-white/70 flex items-center justify-center text-sm font-medium text-charcoal-500">
                Out of Stock
            </span>
        @endif
    </div>

    <div class="mt-3">
        <h3 class="text-sm font-medium text-charcoal-900 group-hover:text-denim-600 transition-colors">{{ $product->name }}</h3>
        <div class="mt-1 flex items-center gap-2">
            @if ($product->is_on_sale)
                <span class="text-sm font-semibold text-charcoal-900">৳{{ number_format($product->discount_price, 2) }}</span>
                <span class="text-xs text-charcoal-400 line-through">৳{{ number_format($product->price, 2) }}</span>
            @else
                <span class="text-sm font-semibold text-charcoal-900">৳{{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        @php $colors = $product->variations->pluck('color_hex', 'color')->filter()->unique(); @endphp
        @if ($colors->isNotEmpty())
            <div class="mt-2 flex items-center gap-1">
                @foreach ($colors->take(4) as $hex)
                    <span class="w-3 h-3 rounded-full border border-charcoal-200" style="background-color: {{ $hex }}"></span>
                @endforeach
            </div>
        @endif
    </div>
</a>
