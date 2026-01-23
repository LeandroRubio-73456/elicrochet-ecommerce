<?php

namespace App\Http\View\Composers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class AdminNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Quotation Alert: Orders in 'quotation' status
        $pendingQuotesCount = Order::where('status', Order::STATUS_QUOTATION)->count();

        // Payment Alert: Orders in 'paid' status
        $paidOrdersCount = Order::where('status', Order::STATUS_PAID)->count();

        // Manufacturing Alert: Orders in 'working' status
        $workingOrdersCount = Order::where('status', Order::STATUS_WORKING)->count();

        // Completed Alert: Recently completed (optional, but requested by user as "completado")
        $completedOrdersCount = Order::where('status', Order::STATUS_COMPLETED)
            ->where('updated_at', '>=', now()->subDays(2))
            ->count();

        // Low Stock Alert: Products with stock < 5
        $lowStockCount = Product::where('stock', '<', 5)->count();

        // Specifics for more detailed notifications
        $pendingQuotes = Order::where('status', Order::STATUS_QUOTATION)->latest()->take(3)->get();
        $paidOrders = Order::where('status', Order::STATUS_PAID)->latest()->take(3)->get();
        $lowStockProducts = Product::where('stock', '<', 5)->orderBy('stock', 'asc')->take(3)->get();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)
            ->where('updated_at', '>=', now()->subDays(2))
            ->latest()
            ->take(3)
            ->get();

        $totalNotifications = $pendingQuotesCount + $paidOrdersCount + $lowStockCount;

        $view->with([
            'pendingQuotes' => $pendingQuotes,
            'paidOrders' => $paidOrders,
            'lowStockProducts' => $lowStockProducts,
            'completedOrders' => $completedOrders,
            'pendingQuotesCount' => $pendingQuotesCount,
            'paidOrdersCount' => $paidOrdersCount,
            'workingOrdersCount' => $workingOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'lowStockCount' => $lowStockCount,
            'totalNotifications' => $totalNotifications,
        ]);
    }
}
