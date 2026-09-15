@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-display font-bold text-charcoal-900">{{ $customer->name }}</h1>
        <a href="{{ route('admin.customers.index') }}" class="text-sm text-charcoal-500 hover:underline">&larr; Back to Customers</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-charcoal-100 font-semibold text-sm">Order History</div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Order #</th>
                            <th class="px-4 py-3">Items</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">{{ $order->items_count }}</td>
                                <td class="px-4 py-3">৳{{ number_format($order->grand_total, 2) }}</td>
                                <td class="px-4 py-3">{{ ucfirst($order->order_status) }}</td>
                                <td class="px-4 py-3 text-charcoal-400">{{ $order->created_at->format('d M, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-denim-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-charcoal-400">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $orders->links() }}</div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Contact</h2>
                <p class="text-sm">{{ $customer->email }}</p>
                <p class="text-sm text-charcoal-500">{{ $customer->phone ?: 'No phone on file' }}</p>
                <p class="text-xs text-charcoal-400 mt-2">Joined {{ $customer->created_at->format('d M, Y') }}</p>
            </div>

            <div class="bg-white border border-charcoal-100 rounded-lg p-6">
                <h2 class="font-semibold mb-3">Saved Addresses</h2>
                @forelse ($customer->addresses as $address)
                    <div class="text-sm border-b border-charcoal-100 last:border-0 py-2">
                        <div class="font-medium">{{ $address->full_name }} {{ $address->is_default ? '(Default)' : '' }}</div>
                        <div class="text-charcoal-500">{{ $address->address_line }}, {{ $address->city }}</div>
                        <div class="text-charcoal-400 text-xs">{{ $address->phone }}</div>
                    </div>
                @empty
                    <p class="text-sm text-charcoal-400">No saved addresses.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
