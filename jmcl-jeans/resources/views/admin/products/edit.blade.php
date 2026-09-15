@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-display font-bold text-charcoal-900">Edit Product</h1>
        <span class="text-sm text-charcoal-400">Total stock: {{ $product->total_stock }}</span>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('admin.products._form')
    </form>
</div>
@endsection
