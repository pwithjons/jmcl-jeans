@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">Edit Subcategory</h1>

    <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}" class="bg-white border border-charcoal-100 rounded-lg p-6">
        @csrf
        @method('put')
        @include('admin.subcategories._form')
    </form>
</div>
@endsection
