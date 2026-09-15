@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Coupon Code</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required
               class="input-field font-mono uppercase" style="text-transform:uppercase">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Discount Type</label>
        <select name="discount_type" required class="input-field">
            <option value="percentage" {{ old('discount_type', $coupon->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
            <option value="fixed" {{ old('discount_type', $coupon->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Discount Value</label>
        <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $coupon->discount_value ?? '') }}" required class="input-field">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Minimum Order Amount (optional)</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" class="input-field">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Usage Limit (optional, blank = unlimited)</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" class="input-field">
    </div>

    <div>
        <label class="block text-sm font-medium text-charcoal-700 mb-1">Expiry Date (optional)</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date', isset($coupon) ? $coupon->expiry_date?->format('Y-m-d') : '') }}" class="input-field">
    </div>

    <div class="md:col-span-2">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded border-charcoal-300"
                   {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
            Active
        </label>
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="btn-primary">{{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}</button>
    <a href="{{ route('admin.coupons.index') }}" class="ml-3 text-sm text-charcoal-500 hover:underline">Cancel</a>
</div>
