<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function edit(): View
    {
        $shipping = ShippingSetting::current();

        return view('admin.shipping.edit', compact('shipping'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inside_city_charge' => ['required', 'numeric', 'min:0'],
            'outside_city_charge' => ['required', 'numeric', 'min:0'],
            'free_delivery_enabled' => ['nullable', 'boolean'],
            'free_delivery_min_amount' => ['nullable', 'required_if:free_delivery_enabled,1', 'numeric', 'min:0'],
        ]);

        $validated['free_delivery_enabled'] = $request->boolean('free_delivery_enabled');

        ShippingSetting::current()->update($validated);

        return redirect()->route('admin.shipping.edit')->with('status', 'Shipping settings updated.');
    }
}
