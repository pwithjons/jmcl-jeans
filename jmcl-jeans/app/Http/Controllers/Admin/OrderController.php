<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $orders = Order::withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->status($request->status))
            ->when($request->filled('search'), fn ($q) => $q->search($request->search))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $statusCounts = Order::selectRaw('order_status, count(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.variation', 'coupon', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $this->orders->updateStatus($order, $validated['order_status']);

        return back()->with('status', 'Order status updated to '.ucfirst($validated['order_status']).'.');
    }
}
