<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::guard('web')->id())
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('account.orders.index', [
            'title' => 'My Orders | JMCL JEANS LTD',
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): View
    {
        // Route model binding finds the order by id regardless of owner,
        // so ownership is enforced here — a customer must never be able
        // to view another customer's order just by guessing the URL.
        abort_unless($order->user_id === Auth::guard('web')->id(), 404);

        $order->load(['items', 'coupon']);

        return view('account.orders.show', [
            'title' => 'Order '.$order->order_number.' | JMCL JEANS LTD',
            'order' => $order,
        ]);
    }
}
