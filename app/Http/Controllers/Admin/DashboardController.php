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

        return view('back.dashboard', compact('totalUsers', 'totalOrders', 'totalSales', 'recentOrders', 'lowStockProducts'));
    }
}
