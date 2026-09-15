<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => User::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::status('pending')->count(),
            'processing_orders' => Order::status('processing')->count(),
            'shipped_orders' => Order::status('shipped')->count(),
            'delivered_orders' => Order::status('delivered')->count(),
            'cancelled_orders' => Order::status('cancelled')->count(),
            'total_sales' => Order::where('order_status', '!=', 'cancelled')->sum('grand_total'),
        ];

        $recentOrders = Order::with('user')->orderByDesc('created_at')->limit(5)->get();

        $lowStockProducts = Product::withSum('variations', 'stock_quantity')
            ->having('variations_sum_stock_quantity', '<=', 5)
            ->having('variations_sum_stock_quantity', '>', 0)
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'admin' => auth('admin')->user(),
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
