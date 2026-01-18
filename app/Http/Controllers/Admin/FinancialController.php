<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    /**
     * Display financial dashboard
     */
    public function index(Request $request)
    {
        // 1. Date Filters
        $period = $request->input('period', 'this_month');
        $customStart = $request->input('date_from');
        $customEnd = $request->input('date_to');

        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfDay();
        $previousStartDate = $now->copy()->subMonth()->startOfMonth(); // For comparison if needed

        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last_week':
                $startDate = $now->copy()->subWeek()->startOfWeek();
                $endDate = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $startDate = Carbon::parse($customStart)->startOfDay();
                    $endDate = Carbon::parse($customEnd)->endOfDay();
                }
                break;
        }

        // Paid/Completed Statuses for Revenue
        $revenueStatuses = [
            Order::STATUS_PAID,
            Order::STATUS_WORKING,
            Order::STATUS_READY_TO_SHIP,
            Order::STATUS_SHIPPED,
            Order::STATUS_COMPLETED,
        ];

        // Base Query for Revenue Analytics (Scoped to Date Range)
        $revenueQuery = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $revenueStatuses);

        // --- KPIs ---

        // 1. Total Income
        $totalIncome = (clone $revenueQuery)->sum('total_amount');

        // 2. Total Paid Orders (for Ticket Avg)
        $totalPaidOrders = (clone $revenueQuery)->count();

        // 3. Average Ticket
        $avgTicket = $totalPaidOrders > 0 ? $totalIncome / $totalPaidOrders : 0;

        // 4. Products Sold (Qty) for the period
        // Join with OrderItems
        $totalProductsSold = (clone $revenueQuery)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum('order_items.quantity');

        // 5. Conversion Rate
        // Logic: Paid Orders / Total Orders (including Quotes/Pending) created in this period
        $allOrdersQuery = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_LINKED) // Exclude technical status
            ->where('orders.status', '!=', Order::STATUS_CANCELLED);
        $totalOrdersCount = $allOrdersQuery->count();

        $conversionRate = $totalOrdersCount > 0
            ? ($totalPaidOrders / $totalOrdersCount) * 100
            : 0;

        // --- Charts Data ---

        // A. Sales Trend (Line Chart)
        // Group by Day if period <= 31 days, else by Month
        $diffInDays = $startDate->diffInDays($endDate);
        $groupByFormat = $diffInDays <= 31
            ? '%Y-%m-%d' // Daily
            : '%Y-%m';   // Monthly

        $dateSelect = DB::getDriverName() === 'sqlite'
            ? "strftime('$groupByFormat', orders.created_at) as date_label"
            : "DATE_FORMAT(orders.created_at, '$groupByFormat') as date_label";

        $salesTrend = (clone $revenueQuery)
            ->select(DB::raw($dateSelect), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date_label')
            ->orderBy('date_label', 'ASC')
            ->get();

        $trendLabels = $salesTrend->pluck('date_label');
        $trendValues = $salesTrend->pluck('total');

        // B. Category Distribution (Donut Chart)
        $categoryDist = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $revenueStatuses)
            ->select('categories.name as category', DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit(6)
            ->get();

        $catLabels = $categoryDist->pluck('category');
        $catValues = $categoryDist->pluck('total_revenue');

        // --- Rankings ---

        // 1. Top 5 Products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $revenueStatuses)
            ->select(
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 2. VIP Clients (Top Spenders)
        $vipClients = (clone $revenueQuery)
            ->select('customer_name', 'customer_email', DB::raw('SUM(total_amount) as total_spent'), DB::raw('COUNT(id) as orders_count'))
            ->groupBy('customer_email', 'customer_name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        return view('admin.finance.index', compact(
            'period', 'startDate', 'endDate',
            'totalIncome', 'avgTicket', 'totalProductsSold', 'conversionRate',
            'trendLabels', 'trendValues',
            'catLabels', 'catValues',
            'topProducts', 'vipClients'
        ));
    }

    /**
     * Export Report to CSV
     */
    public function export(Request $request)
    {
        // Re-apply filters to export what is currently viewed
        $period = $request->input('period', 'this_month');
        $customStart = $request->input('date_from');
        $customEnd = $request->input('date_to');

        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last_week':
                $startDate = $now->copy()->subWeek()->startOfWeek();
                $endDate = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $startDate = Carbon::parse($customStart)->startOfDay();
                    $endDate = Carbon::parse($customEnd)->endOfDay();
                }
                break;
        }

        $fileName = 'elicrochet_ventas_'.date('Y-m-d_H-i').'.csv';

        // Export logic: Get all paid/completed orders in range
        $orders = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED, Order::STATUS_WORKING, Order::STATUS_READY_TO_SHIP])
            ->orderBy('orders.created_at', 'desc')
            ->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Fecha', 'Cliente', 'Email', 'Estado', 'Tipo', 'Total'];

        $callback = function () use ($orders, $columns, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM correctly

            // Metadata
            fputcsv($file, ['Reporte de Ventas - EliCrochet'], ';');
            fputcsv($file, ['Periodo:', $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y')], ';');
            fputcsv($file, [], ';');

            fputcsv($file, $columns, ';');

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name,
                    $order->customer_email,
                    ucfirst($order->status),
                    ucfirst($order->type),
                    number_format($order->total_amount, 2, ',', '.'),
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
