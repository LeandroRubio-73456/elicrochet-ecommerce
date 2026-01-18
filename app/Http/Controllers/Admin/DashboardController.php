<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPIs
        $totalUsers = \App\Models\User::count();
        $totalOrders = \App\Models\Order::count();
        $totalSales = \App\Models\Order::whereIn('status', ['paid', 'completed', 'shipped', 'ready_to_ship'])->sum('total_amount');

        // 2. Recent Orders (Top 5)
        $recentOrders = \App\Models\Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // 3. Low Stock Products (Less than 5)
        $lowStockProducts = \App\Models\Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->take(5)->get();

        // 4. Recent Reviews (Top 5)
        $recentReviews = \App\Models\Review::with(['user', 'product'])->orderBy('created_at', 'desc')->take(5)->get();

        // Status Balance (KPI)
        $statusCounts = \App\Models\Order::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount = $statusCounts['pending_payment'] ?? 0;
        $workingCount = ($statusCounts['working'] ?? 0) + ($statusCounts['ready_to_ship'] ?? 0);
        $paidCount = ($statusCounts['paid'] ?? 0) + ($statusCounts['shipped'] ?? 0) + ($statusCounts['completed'] ?? 0) + ($statusCounts['delivered'] ?? 0);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalSales',
            'recentOrders',
            'lowStockProducts',
            'recentReviews',
            'pendingCount',
            'workingCount',
            'paidCount'
        ));
    }
}
