@extends('admin.layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">Sales Report</h1>

    <form method="GET" class="bg-white border border-charcoal-100 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" class="input-field">
        </div>
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" class="input-field">
        </div>
        <button type="submit" class="btn-secondary !px-4 !py-2">Update</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Total Sales</div>
            <div class="text-2xl font-display font-bold mt-1">৳{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Orders</div>
            <div class="text-2xl font-display font-bold mt-1">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white border border-charcoal-100 rounded-lg p-5">
            <div class="text-xs text-charcoal-500 uppercase">Average Order Value</div>
            <div class="text-2xl font-display font-bold mt-1">৳{{ number_format($averageOrderValue, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-charcoal-100 font-semibold text-sm">Sales by Day</div>
            <table class="w-full text-sm text-left">
                <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                    <tr><th class="px-4 py-2">Date</th><th class="px-4 py-2">Orders</th><th class="px-4 py-2">Sales</th></tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse ($salesByDay as $day)
                        <tr>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($day->day)->format('d M') }}</td>
                            <td class="px-4 py-2">{{ $day->orders }}</td>
                            <td class="px-4 py-2">৳{{ number_format($day->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-charcoal-400">No sales in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-charcoal-100 font-semibold text-sm">Top Products (by units sold)</div>
            <table class="w-full text-sm text-left">
                <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                    <tr><th class="px-4 py-2">Product</th><th class="px-4 py-2">Units</th><th class="px-4 py-2">Revenue</th></tr>
                </thead>
                <tbody class="divide-y divide-charcoal-100">
                    @forelse ($topProducts as $product)
                        <tr>
                            <td class="px-4 py-2">{{ $product->product_name }}</td>
                            <td class="px-4 py-2">{{ $product->units_sold }}</td>
                            <td class="px-4 py-2">৳{{ number_format($product->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-charcoal-400">No sales in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
