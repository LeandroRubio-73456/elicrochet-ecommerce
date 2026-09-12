<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPIs
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalSales = Order::whereIn('status', ['paid', 'completed', 'shipped', 'ready_to_ship'])->sum('total_amount');

        // 2. Recent Orders (Top 5)
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // 3. Low Stock Products (Less than 5)
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->take(5)->get();

        // 4. Recent Reviews (Top 5)
        $recentReviews = Review::with(['user', 'product'])->orderBy('created_at', 'desc')->take(5)->get();

        // Status Balance (KPI)
        $statusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount = $statusCounts['pending_payment'] ?? 0;
        $workingCount = ($statusCounts['working'] ?? 0) + ($statusCounts['ready_to_ship'] ?? 0);
        $paidCount = ($statusCounts['paid'] ?? 0) + ($statusCounts['shipped'] ?? 0) + ($statusCounts['completed'] ?? 0) + ($statusCounts['delivered'] ?? 0);

        // 6. Growth (Current Month vs Previous)
        $currentMonthSales = Order::whereIn('status', ['paid', 'completed', 'shipped', 'ready_to_ship'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $lastMonthSales = Order::whereIn('status', ['paid', 'completed', 'shipped', 'ready_to_ship'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $monthlyGrowth = $lastMonthSales > 0 ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalSales',
            'recentOrders',
            'lowStockProducts',
            'recentReviews',
            'pendingCount',
            'workingCount',
            'paidCount',
            'monthlyGrowth'
        ));
    }
}
