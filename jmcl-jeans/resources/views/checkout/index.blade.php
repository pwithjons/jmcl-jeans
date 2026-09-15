@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="section-heading mb-8">Checkout</h1>

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <form method="POST" action="{{ route('checkout.store') }}" class="md:col-span-2 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name', $defaultAddress?->full_name ?? auth('web')->user()?->name) }}" required class="input-field">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $defaultAddress?->phone ?? auth('web')->user()?->phone) }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Email (optional)</label>
                    <input type="email" name="email" value="{{ old('email', auth('web')->user()?->email) }}" class="input-field">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Delivery Address</label>
                <textarea name="address" rows="2" required class="input-field">{{ old('address', $defaultAddress?->address_line ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $defaultAddress?->city ?? '') }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Delivery Area (optional)</label>
                    <input type="text" name="delivery_area" value="{{ old('delivery_area', $defaultAddress?->delivery_area ?? '') }}" class="input-field">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-2">Delivery Zone</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="delivery_zone" value="inside" {{ old('delivery_zone', 'inside') === 'inside' ? 'checked' : '' }} required>
                        Inside City
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="delivery_zone" value="outside" {{ old('delivery_zone') === 'outside' ? 'checked' : '' }}>
                        Outside City
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Order Notes (optional)</label>
                <textarea name="order_notes" rows="2" class="input-field">{{ old('order_notes') }}</textarea>
            </div>

            <div class="bg-charcoal-50 p-4 text-sm">
                <strong>Payment Method:</strong> Cash on Delivery — pay when your order arrives. No card or online payment required.
            </div>

            <button type="submit" class="btn-primary w-full">Place Order</button>
        </form>

        <div class="bg-charcoal-50 p-6 h-fit">
            <h2 class="font-semibold mb-4">Order Summary</h2>
            <div class="space-y-3 divide-y divide-charcoal-200">
                @foreach ($items as $item)
                    <div class="flex justify-between text-sm pt-3 first:pt-0">
                        <span>{{ $item->product->name }} ({{ $item->variation->size }}/{{ $item->variation->color }}) &times; {{ $item->quantity }}</span>
                        <span>৳{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-charcoal-200 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-charcoal-500">Subtotal</span><span>৳{{ number_format($subtotal, 2) }}</span></div>
                @if ($appliedCoupon)
                    <div class="flex justify-between text-green-600">
                        <span>Coupon ({{ $appliedCoupon->code }})</span><span>-৳{{ number_format($discount, 2) }}</span>
                    </div>
                @endif
                <p class="text-xs text-charcoal-400">Delivery charge will be added based on your zone above.</p>
            </div>
        </div>
    </div>
</div>
@endsection
