@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <h1 class="section-heading mb-8">Your Cart</h1>

    @if ($items->isEmpty())
        <div class="text-center py-20">
            <p class="text-charcoal-400 mb-4">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Continue Shopping</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="md:col-span-2 divide-y divide-charcoal-100">
                @foreach ($items as $item)
                    <div class="py-5 flex gap-4">
                        <div class="w-20 h-24 bg-charcoal-50 shrink-0 overflow-hidden">
                            @if ($item->product->primary_image)
                                <img src="{{ $item->product->primary_image->url }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="flex-1">
                            <a href="{{ route('products.show', $item->product) }}" class="font-medium hover:text-denim-600">{{ $item->product->name }}</a>
                            <p class="text-sm text-charcoal-500">{{ $item->variation->size }} / {{ $item->variation->color }}</p>
                            <p class="text-sm font-semibold mt-1">৳{{ number_format($item->unit_price, 2) }}</p>

                            <div class="mt-3 flex items-center gap-4">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center border border-charcoal-200 rounded">
                                    @csrf @method('patch')
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="px-3 py-1 text-charcoal-500">-</button>
                                    <span class="px-3 text-sm">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                            {{ $item->quantity >= $item->variation->stock_quantity ? 'disabled' : '' }}
                                            class="px-3 py-1 text-charcoal-500 {{ $item->quantity >= $item->variation->stock_quantity ? 'opacity-30' : '' }}">+</button>
                                </form>

                                <form method="POST" action="{{ route('cart.destroy', $item->id) }}">
                                    @csrf @method('delete')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Remove</button>
                                </form>
                            </div>
                            @if ($item->quantity >= $item->variation->stock_quantity)
                                <p class="text-xs text-charcoal-400 mt-1">Max available stock reached.</p>
                            @endif
                        </div>

                        <div class="text-sm font-semibold">৳{{ number_format($item->line_total, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="bg-charcoal-50 p-6 h-fit">
                <h2 class="font-semibold mb-4">Order Summary</h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-charcoal-500">Subtotal</span><span>৳{{ number_format($subtotal, 2) }}</span></div>
                    @if ($appliedCoupon)
                        <div class="flex justify-between text-green-600">
                            <span>Coupon ({{ $appliedCoupon->code }})</span>
                            <span>-৳{{ number_format($discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-semibold border-t border-charcoal-200 pt-2">
                        <span>Total</span><span>৳{{ number_format($subtotal - $discount, 2) }}</span>
                    </div>
                    <p class="text-xs text-charcoal-400">Delivery charge calculated at checkout.</p>
                </div>

                <div class="mt-5">
                    @if ($appliedCoupon)
                        <form method="POST" action="{{ route('cart.coupon.remove') }}" class="flex items-center justify-between text-xs">
                            @csrf @method('delete')
                            <span class="text-charcoal-500">Applied: <strong>{{ $appliedCoupon->code }}</strong></span>
                            <button type="submit" class="text-red-600 hover:underline">Remove</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="Coupon code" class="input-field text-sm flex-1">
                            <button type="submit" class="btn-secondary !px-3 !py-2 text-xs">Apply</button>
                        </form>
                    @endif
                </div>

                <a href="{{ route('checkout.index') }}" class="btn-primary w-full mt-6 block text-center">Proceed to Checkout</a>
            </div>
        </div>
    @endif
</div>
@endsection
