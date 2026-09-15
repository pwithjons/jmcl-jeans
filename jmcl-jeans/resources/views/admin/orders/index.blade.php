@extends('admin.layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">Orders</h1>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    {{-- Status tabs --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @php
            $statuses = ['' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
        @endphp
        @foreach ($statuses as $value => $label)
            <a href="{{ route('admin.orders.index', array_filter(request()->query() + ['status' => $value])) }}"
               class="px-3 py-1.5 rounded-full text-sm border {{ request('status', '') === $value ? 'bg-charcoal-900 text-white border-charcoal-900' : 'border-charcoal-200 text-charcoal-600 hover:border-charcoal-400' }}">
                {{ $label }}
                @if ($value && ($statusCounts[$value] ?? 0))
                    <span class="opacity-70">({{ $statusCounts[$value] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" class="bg-white border border-charcoal-100 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, name, or phone" class="input-field w-56">
        </div>
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field">
        </div>
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field">
        </div>
        <button type="submit" class="btn-secondary !px-4 !py-2">Filter</button>
    </form>

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Order #</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Items</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Payment</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Placed</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">
                            <div>{{ $order->full_name }}</div>
                            <div class="text-xs text-charcoal-400">{{ $order->phone }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $order->items_count }}</td>
                        <td class="px-4 py-3">৳{{ number_format($order->grand_total, 2) }}</td>
                        <td class="px-4 py-3 text-charcoal-500">{{ ucfirst($order->payment_status) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'text-charcoal-500', 'processing' => 'text-denim-600',
                                    'shipped' => 'text-gold-500', 'delivered' => 'text-green-600', 'cancelled' => 'text-red-600',
                                ];
                            @endphp
                            <span class="{{ $statusColors[$order->order_status] ?? '' }} font-medium">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-charcoal-400">{{ $order->created_at->format('d M, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-denim-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-charcoal-400">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
