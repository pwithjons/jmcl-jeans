<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

        // Cancelled orders never counted as revenue.
        $baseQuery = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('order_status', '!=', 'cancelled');

        $totalSales = (clone $baseQuery)->sum('grand_total');
        $totalOrders = (clone $baseQuery)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $salesByDay = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as day, SUM(grand_total) as total, COUNT(*) as orders')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->where('orders.order_status', '!=', 'cancelled')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as units_sold, SUM(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'totalSales', 'totalOrders', 'averageOrderValue', 'salesByDay', 'topProducts', 'dateFrom', 'dateTo'
        ));
    }
}
