@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl py-12">

    @include('partials.account-nav')

    <h1 class="section-heading mb-6">My Orders</h1>

    @if ($orders->isEmpty())
        <div class="text-center py-16">
            <p class="text-charcoal-400 mb-4">You haven't placed any orders yet.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Start Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                @php
                    $statusColors = [
                        'pending' => 'bg-charcoal-100 text-charcoal-600',
                        'processing' => 'bg-denim-100 text-denim-700',
                        'shipped' => 'bg-gold-400/20 text-gold-500',
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <a href="{{ route('account.orders.show', $order) }}"
                   class="block border border-charcoal-100 rounded-lg p-5 hover:border-charcoal-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $order->order_number }}</div>
                            <div class="text-xs text-charcoal-400 mt-1">
                                {{ $order->created_at->format('d M, Y') }} · {{ $order->items_count }} item(s)
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">৳{{ number_format($order->grand_total, 2) }}</div>
                            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full {{ $statusColors[$order->order_status] ?? '' }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
