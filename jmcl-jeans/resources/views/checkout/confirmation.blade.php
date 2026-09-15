@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 max-w-xl text-center">
    <div class="text-green-600 text-4xl mb-4">&check;</div>
    <h1 class="section-heading">Thank You!</h1>
    <p class="text-charcoal-500 mt-2">Your order has been placed successfully.</p>

    <div class="bg-charcoal-50 mt-8 p-6 text-left">
        <div class="flex justify-between text-sm mb-4">
            <span class="text-charcoal-500">Order Number</span>
            <span class="font-semibold">{{ $order->order_number }}</span>
        </div>

        <div class="space-y-2 divide-y divide-charcoal-200">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm pt-2 first:pt-0">
                    <span>{{ $item->product_name }} ({{ $item->size }}/{{ $item->color }}) &times; {{ $item->quantity }}</span>
                    <span>৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-charcoal-200 space-y-1 text-sm">
            <div class="flex justify-between"><span class="text-charcoal-500">Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-charcoal-500">Delivery</span><span>৳{{ number_format($order->delivery_charge, 2) }}</span></div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ number_format($order->discount, 2) }}</span></div>
            @endif
            <div class="flex justify-between font-semibold pt-1"><span>Grand Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span></div>
        </div>

        <p class="text-xs text-charcoal-400 mt-4">Payment: Cash on Delivery. We'll deliver to {{ $order->address }}, {{ $order->city }}.</p>
    </div>

    <a href="{{ route('shop.index') }}" class="btn-primary mt-8 inline-block">Continue Shopping</a>
</div>
@endsection
