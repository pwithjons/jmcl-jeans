@extends('admin.layouts.app')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-charcoal-900">Coupons</h1>
            <p class="text-charcoal-500 mt-1">Percentage or fixed-amount discount codes.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary">+ New Coupon</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Discount</th>
                    <th class="px-4 py-3">Min Order</th>
                    <th class="px-4 py-3">Usage</th>
                    <th class="px-4 py-3">Expiry</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 font-mono font-medium">{{ $coupon->code }}</td>
                        <td class="px-4 py-3">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '৳'.number_format($coupon->discount_value, 2) }}
                        </td>
                        <td class="px-4 py-3 text-charcoal-500">
                            {{ $coupon->min_order_amount ? '৳'.number_format($coupon->min_order_amount, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-charcoal-500">
                            {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}
                        </td>
                        <td class="px-4 py-3 text-charcoal-500">
                            {{ $coupon->expiry_date?->format('d M, Y') ?? 'No expiry' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $coupon->is_active ? 'text-green-600' : 'text-charcoal-400' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-denim-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline"
                                  onsubmit="return confirm('Delete this coupon?');">
                                @csrf @method('delete')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-charcoal-400">No coupons yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $coupons->links() }}</div>
</div>
@endsection
