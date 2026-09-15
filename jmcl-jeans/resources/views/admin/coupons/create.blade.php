@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">New Coupon</h1>

    <form method="POST" action="{{ route('admin.coupons.store') }}" class="bg-white border border-charcoal-100 rounded-lg p-6">
        @csrf
        @include('admin.coupons._form')
    </form>
</div>
@endsection
