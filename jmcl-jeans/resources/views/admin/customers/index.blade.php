@extends('admin.layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">Customers</h1>

    <form method="GET" class="bg-white border border-charcoal-100 rounded-lg p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-charcoal-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or phone" class="input-field w-64">
        </div>
        <button type="submit" class="btn-secondary !px-4 !py-2">Search</button>
        @if (request()->filled('search'))
            <a href="{{ route('admin.customers.index') }}" class="text-sm text-charcoal-500 hover:underline">Clear</a>
        @endif
    </form>

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Orders</th>
                    <th class="px-4 py-3">Total Spent</th>
                    <th class="px-4 py-3">Joined</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $customer->email }}</td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $customer->phone ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                        <td class="px-4 py-3">৳{{ number_format($customer->orders_sum_grand_total ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-charcoal-400">{{ $customer->created_at->format('d M, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-denim-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-charcoal-400">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
</div>
@endsection
