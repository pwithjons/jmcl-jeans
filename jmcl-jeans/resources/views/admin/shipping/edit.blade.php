@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl">
    <h1 class="text-2xl font-display font-bold text-charcoal-900 mb-6">Shipping Settings</h1>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.shipping.update') }}" class="bg-white border border-charcoal-100 rounded-lg p-6 space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Inside City Delivery Charge</label>
            <input type="number" step="0.01" min="0" name="inside_city_charge"
                   value="{{ old('inside_city_charge', $shipping->inside_city_charge) }}" required class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Outside City Delivery Charge</label>
            <input type="number" step="0.01" min="0" name="outside_city_charge"
                   value="{{ old('outside_city_charge', $shipping->outside_city_charge) }}" required class="input-field">
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="free_delivery_enabled" value="1" id="free_delivery_enabled"
                       onchange="document.getElementById('free_min_wrap').classList.toggle('hidden', !this.checked)"
                       class="rounded border-charcoal-300"
                       {{ old('free_delivery_enabled', $shipping->free_delivery_enabled) ? 'checked' : '' }}>
                Enable free delivery above a minimum order amount
            </label>
        </div>

        <div id="free_min_wrap" class="{{ old('free_delivery_enabled', $shipping->free_delivery_enabled) ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Free Delivery Minimum Order Amount</label>
            <input type="number" step="0.01" min="0" name="free_delivery_min_amount"
                   value="{{ old('free_delivery_min_amount', $shipping->free_delivery_min_amount) }}" class="input-field">
        </div>

        <button type="submit" class="btn-primary">Save Shipping Settings</button>
    </form>
</div>
@endsection
