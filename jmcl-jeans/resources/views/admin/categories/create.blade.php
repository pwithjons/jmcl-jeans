@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">New Category</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="bg-white border border-charcoal-100 rounded-lg p-6">
        @csrf
        @include('admin.categories._form')
    </form>
</div>
@endsection
