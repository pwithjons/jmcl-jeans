@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl">
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">New Product</h1>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
    </form>
</div>
@endsection
