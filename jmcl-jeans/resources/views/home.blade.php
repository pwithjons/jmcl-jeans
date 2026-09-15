@extends('layouts.app')

@section('content')

{{-- Hero: asymmetric split, staggered image cluster instead of a centered banner --}}
<section class="container mx-auto px-4 pt-10 pb-16 md:pt-16 md:pb-24">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center">
        <div class="md:col-span-6 lg:col-span-5">
            <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05] text-charcoal-900">
                {{ $heroTitle }}
            </h1>
            @if ($heroSubtitle)
                <p class="mt-5 text-charcoal-500 text-base md:text-lg max-w-md">{{ $heroSubtitle }}</p>
            @endif
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('shop.index') }}" class="btn-primary">Shop New Arrivals</a>
                <a href="{{ route('shop.index') }}?sort=popularity" class="btn-secondary">Explore Bestsellers</a>
            </div>
        </div>

        <div class="md:col-span-6 lg:col-span-7">
            <div class="grid grid-cols-3 gap-3 md:gap-4">
                @php $heroImages = $featuredProducts->flatMap(fn($p) => $p->images)->take(3); @endphp
                <div class="col-span-2 row-span-2 aspect-[4/5] bg-charcoal-50 overflow-hidden">
                    @if ($heroImages->get(0))
                        <img src="{{ $heroImages->get(0)->url }}" class="w-full h-full object-cover" alt="Featured denim">
                    @endif
                </div>
                <div class="aspect-square bg-charcoal-50 overflow-hidden translate-y-4">
                    @if ($heroImages->get(1))
                        <img src="{{ $heroImages->get(1)->url }}" class="w-full h-full object-cover" alt="Featured denim">
                    @endif
                </div>
                <div class="aspect-square bg-charcoal-50 overflow-hidden -translate-y-2">
                    @if ($heroImages->get(2))
                        <img src="{{ $heroImages->get(2)->url }}" class="w-full h-full object-cover" alt="Featured denim">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Category strip: flat image tiles, no card shadows --}}
@if ($categories->isNotEmpty())
<section class="container mx-auto px-4 pb-16">
    <h2 class="section-heading mb-6">Shop by Category</h2>
    <div class="flex gap-4 overflow-x-auto pb-2 -mx-4 px-4 md:mx-0 md:px-0 md:grid md:grid-cols-5">
        @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="relative shrink-0 w-40 md:w-auto aspect-[3/4] bg-charcoal-100 overflow-hidden group">
                @if ($category->image_url)
                    <img src="{{ $category->image_url }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $category->name }}">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-charcoal-950/70 via-transparent to-transparent"></div>
                <span class="absolute bottom-3 left-3 text-white font-medium text-sm">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Featured products --}}
@if ($featuredProducts->isNotEmpty())
<section class="container mx-auto px-4 pb-16">
    <div class="flex items-end justify-between mb-6">
        <h2 class="section-heading">Featured Pieces</h2>
        <a href="{{ route('shop.index') }}" class="text-sm text-denim-600 hover:underline hidden md:block">View all products</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
        @foreach ($featuredProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Promotional banner — pulled from real shipping settings, not decoration --}}
<section class="bg-charcoal-900 text-white">
    <div class="container mx-auto px-4 py-10 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
        <div>
            <h3 class="font-display text-2xl font-bold">Cash on Delivery, Nationwide</h3>
            <p class="text-charcoal-300 mt-1 text-sm">
                @if ($shipping->free_delivery_enabled && $shipping->free_delivery_min_amount)
                    Free delivery on orders over ৳{{ number_format($shipping->free_delivery_min_amount, 0) }}.
                @else
                    Fast, reliable delivery across the country.
                @endif
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-secondary !border-white !text-white hover:!bg-white hover:!text-charcoal-900">Start Shopping</a>
    </div>
</section>

{{-- New arrivals --}}
@if ($newArrivals->isNotEmpty())
<section class="container mx-auto px-4 py-16">
    <h2 class="section-heading mb-6">New Arrivals</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
        @foreach ($newArrivals->take(8) as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Why JMCL — plain text blocks with a rule divider, not icon cards --}}
<section class="border-y border-charcoal-100">
    <div class="container mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-charcoal-100">
        <div class="py-6 md:py-0 md:px-8 first:pt-0 md:first:pl-0">
            <h3 class="font-display text-lg font-bold mb-2">Selvedge-Grade Denim</h3>
            <p class="text-sm text-charcoal-500">Sourced fabric built to hold shape and color wash after wash.</p>
        </div>
        <div class="py-6 md:py-0 md:px-8">
            <h3 class="font-display text-lg font-bold mb-2">True-to-Size Fits</h3>
            <p class="text-sm text-charcoal-500">Every style available from S to XXL, cut consistently across colors.</p>
        </div>
        <div class="py-6 md:py-0 md:px-8 last:pb-0 md:last:pr-0">
            <h3 class="font-display text-lg font-bold mb-2">Cash on Delivery</h3>
            <p class="text-sm text-charcoal-500">Pay when it arrives — no card or online payment required.</p>
        </div>
    </div>
</section>

{{-- Testimonials — plain quotes, no stock avatars --}}
<section class="container mx-auto px-4 py-16">
    <h2 class="section-heading mb-8 text-center">What Customers Say</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <blockquote class="text-center">
            <p class="text-charcoal-700">"The slim fit jeans held their shape after a dozen washes. Genuinely the best denim I've bought locally."</p>
            <footer class="mt-3 text-sm text-charcoal-400">— Tanvir H.</footer>
        </blockquote>
        <blockquote class="text-center">
            <p class="text-charcoal-700">"Ordered a jacket on COD, arrived in two days. Sizing matched the chart exactly."</p>
            <footer class="mt-3 text-sm text-charcoal-400">— Nusrat J.</footer>
        </blockquote>
        <blockquote class="text-center">
            <p class="text-charcoal-700">"Finally a denim brand that fits true to size. Ordering a second pair already."</p>
            <footer class="mt-3 text-sm text-charcoal-400">— Kamal R.</footer>
        </blockquote>
    </div>
</section>

{{-- Newsletter --}}
<section class="bg-charcoal-50">
    <div class="container mx-auto px-4 py-14 text-center max-w-lg">
        <h3 class="font-display text-2xl font-bold">Stay in the Loop</h3>
        <p class="text-charcoal-500 mt-2 text-sm">New drops and restocks, straight to your inbox. No spam.</p>
        <form class="mt-5 flex gap-2">
            <input type="email" required placeholder="you@example.com" class="input-field flex-1">
            <button type="submit" class="btn-primary shrink-0">Subscribe</button>
        </form>
    </div>
</section>

@endsection
