@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="section-heading">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="text-charcoal-500 mt-2 max-w-2xl">{{ $category->description }}</p>
    @endif

    @if ($subcategories->isNotEmpty())
        <div class="flex flex-wrap gap-2 mt-6">
            <a href="{{ route('categories.show', $category) }}"
               class="px-3 py-1.5 rounded-full text-sm border {{ ! request('subcategory') ? 'bg-charcoal-900 text-white border-charcoal-900' : 'border-charcoal-200 text-charcoal-600' }}">
                All
            </a>
            @foreach ($subcategories as $sub)
                <a href="{{ route('categories.show', $category) }}?subcategory={{ $sub->id }}"
                   class="px-3 py-1.5 rounded-full text-sm border {{ request('subcategory') == $sub->id ? 'bg-charcoal-900 text-white border-charcoal-900' : 'border-charcoal-200 text-charcoal-600' }}">
                    {{ $sub->name }}
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-8">
        @if ($products->isEmpty())
            <div class="text-center py-20 text-charcoal-400">No products in this category yet.</div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
