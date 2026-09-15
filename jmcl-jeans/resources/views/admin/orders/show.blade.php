@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-charcoal-900">Order {{ $order->order_number }}</h1>
            <p class="text-charcoal-500 mt-1">Placed on {{ $order->created_at->format('d M, Y — h:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-charcoal-500 hover:underline">&larr; Back to Orders</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left: items + totals --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Size/Color</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->product_name }}</div>
                                    <div class="text-xs text-charcoal-400">{{ $item->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-charcoal-500">{{ $item->size }} / {{ $item->color }}</td>
                                <td class="px-4 py-3">৳{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 font-medium">৳{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Order Notes</h2>
                <p class="text-sm text-charcoal-500">{{ $order->order_notes ?: 'No notes provided.' }}</p>
            </div>
        </div>

        {{-- Right: customer, delivery, totals, status --}}
        <div class="space-y-6">
            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Customer</h2>
                <p class="text-sm">{{ $order->full_name }}</p>
                <p class="text-sm text-charcoal-500">{{ $order->phone }}</p>
                @if ($order->email)
                    <p class="text-sm text-charcoal-500">{{ $order->email }}</p>
                @endif
                @if ($order->user)
                    <a href="{{ route('admin.customers.show', $order->user) }}" class="text-xs text-denim-600 hover:underline">View customer profile</a>
                @else
                    <p class="text-xs text-charcoal-400 mt-1">Guest checkout</p>
                @endif
            </div>

            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Delivery Address</h2>
                <p class="text-sm text-charcoal-600">{{ $order->address }}</p>
                <p class="text-sm text-charcoal-600">{{ $order->city }}@if($order->delivery_area), {{ $order->delivery_area }}@endif</p>
            </div>

            <div class="bg-white border border-charcoal-100 rounded-lg p-6 space-y-2">
                <h2 class="font-semibold mb-1">Totals</h2>
                <div class="flex justify-between text-sm"><span class="text-charcoal-500">Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-charcoal-500">Delivery</span><span>৳{{ number_format($order->delivery_charge, 2) }}</span></div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span>
                        <span>-৳{{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-semibold border-t border-charcoal-100 pt-2 mt-2">
                    <span>Grand Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span>
                </div>
                <div class="text-xs text-charcoal-400 pt-2">
                    Payment: {{ str_replace('_', ' ', ucfirst($order->payment_method)) }} — {{ ucfirst($order->payment_status) }}
                </div>
            </div>

            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Order Status</h2>
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-3">
                    @csrf
                    @method('patch')
                    <select name="order_status" class="input-field">
                        @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $order->order_status === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary w-full">Update Status</button>
                </form>
                @if ($order->order_status !== 'cancelled')
                    <p class="text-xs text-charcoal-400 mt-2">Cancelling releases reserved stock back to inventory.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
