@extends('admin.layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-display font-bold text-charcoal-900">Welcome, {{ $admin->name }}</h1>
    <p class="text-charcoal-500 mt-1">
        Signed in as <span class="font-medium">{{ $admin->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>.
    </p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Total Sales</div>
            <div class="text-2xl font-display font-bold mt-1">৳{{ number_format($stats['total_sales'], 2) }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Total Orders</div>
            <div class="text-2xl font-display font-bold mt-1">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Products</div>
            <div class="text-2xl font-display font-bold mt-1">{{ $stats['total_products'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Customers</div>
            <div class="text-2xl font-display font-bold mt-1">{{ $stats['total_customers'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
        <div class="bg-white border border-charcoal-100 rounded-lg p-4 text-center">
            <div class="text-xs text-charcoal-500 uppercase">Pending</div>
            <div class="text-xl font-bold mt-1">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-4 text-center">
            <div class="text-xs text-charcoal-500 uppercase">Processing</div>
            <div class="text-xl font-bold mt-1 text-denim-600">{{ $stats['processing_orders'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-4 text-center">
            <div class="text-xs text-charcoal-500 uppercase">Shipped</div>
            <div class="text-xl font-bold mt-1 text-gold-500">{{ $stats['shipped_orders'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-4 text-center">
            <div class="text-xs text-charcoal-500 uppercase">Delivered</div>
            <div class="text-xl font-bold mt-1 text-green-600">{{ $stats['delivered_orders'] }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-4 text-center">
            <div class="text-xs text-charcoal-500 uppercase">Cancelled</div>
            <div class="text-xl font-bold mt-1 text-red-600">{{ $stats['cancelled_orders'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-charcoal-100 font-semibold text-sm flex items-center justify-between">
                Recent Orders
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-denim-600 hover:underline font-normal">View all</a>
            </div>
            <table class="w-full text-sm text-left">
                <tbody class="divide-y divide-charcoal-100">
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="px-4 py-2 font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-4 py-2 text-charcoal-500">{{ $order->full_name }}</td>
                            <td class="px-4 py-2">৳{{ number_format($order->grand_total, 2) }}</td>
                            <td class="px-4 py-2 text-charcoal-500">{{ ucfirst($order->order_status) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center text-charcoal-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-charcoal-100 font-semibold text-sm flex items-center justify-between">
                Low Stock Alert (≤ 5 units)
                <a href="{{ route('admin.products.index') }}" class="text-xs text-denim-600 hover:underline font-normal">View products</a>
            </div>
            <table class="w-full text-sm text-left">
                <tbody class="divide-y divide-charcoal-100">
                    @forelse ($lowStockProducts as $product)
                        <tr>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="hover:underline">{{ $product->name }}</a>
                            </td>
                            <td class="px-4 py-2 text-red-600 text-right">{{ $product->variations_sum_stock_quantity }} left</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center text-charcoal-400">No low-stock products right now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
