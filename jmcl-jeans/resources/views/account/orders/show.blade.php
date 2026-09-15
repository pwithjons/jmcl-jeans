@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl py-12">

    @include('partials.account-nav')

    <div class="flex items-center justify-between mb-6">
        <h1 class="section-heading">Order {{ $order->order_number }}</h1>
        <a href="{{ route('account.orders.index') }}" class="text-sm text-charcoal-500 hover:underline">&larr; All Orders</a>
    </div>

    @if ($order->order_status === 'cancelled')
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 mb-6">
            This order was cancelled.
        </div>
    @else
        @php
            $steps = ['pending' => 'Order Placed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
            $stepKeys = array_keys($steps);
            $currentIndex = array_search($order->order_status, $stepKeys);
        @endphp
        <div class="flex items-center mb-10">
            @foreach ($steps as $key => $label)
                @php $isDone = array_search($key, $stepKeys) <= $currentIndex; @endphp
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                        {{ $isDone ? 'bg-charcoal-900 text-white' : 'bg-charcoal-100 text-charcoal-400' }}">
                        {{ $loop->iteration }}
                    </div>
                    <span class="text-xs mt-2 {{ $isDone ? 'text-charcoal-900 font-medium' : 'text-charcoal-400' }}">{{ $label }}</span>
                </div>
                @if (! $loop->last)
                    <div class="flex-1 h-0.5 -mt-6 {{ array_search($key, $stepKeys) < $currentIndex ? 'bg-charcoal-900' : 'bg-charcoal-100' }}"></div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden mb-6">
        <table class="w-full text-sm text-left">
            <tbody class="divide-y divide-charcoal-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->product_name }}</div>
                            <div class="text-xs text-charcoal-400">{{ $item->size }} / {{ $item->color }} &times; {{ $item->quantity }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-charcoal-50 p-6 space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-charcoal-500">Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
        <div class="flex justify-between"><span class="text-charcoal-500">Delivery</span><span>৳{{ number_format($order->delivery_charge, 2) }}</span></div>
        @if ($order->discount > 0)
            <div class="flex justify-between text-green-600">
                <span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span>
                <span>-৳{{ number_format($order->discount, 2) }}</span>
            </div>
        @endif
        <div class="flex justify-between font-semibold border-t border-charcoal-200 pt-2 mt-2">
            <span>Grand Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span>
        </div>
        <div class="pt-3 border-t border-charcoal-200 mt-3 text-charcoal-500">
            <p>Delivering to: {{ $order->address }}, {{ $order->city }}</p>
            <p class="mt-1">Payment: Cash on Delivery — {{ ucfirst($order->payment_status) }}</p>
        </div>
    </div>
</div>
@endsection
