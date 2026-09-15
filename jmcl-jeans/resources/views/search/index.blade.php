@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="section-heading">
        @if ($term)
            Search results for "{{ $term }}"
        @else
            Search
        @endif
    </h1>
    <p class="text-charcoal-500 mt-2">{{ $products->total() }} products found</p>

    <div class="mt-8">
        @if ($products->isEmpty())
            <div class="text-center py-20 text-charcoal-400">
                No products matched your search.
                <a href="{{ route('shop.index') }}" class="text-denim-600 hover:underline">Browse all products</a> instead.
            </div>
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
