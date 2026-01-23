<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()
            ->whereNot(function ($query) {
                $query->whereIn('type', [Order::TYPE_STOCK, 'stock'])
                    ->where('status', Order::STATUS_PENDING_PAYMENT);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('front.account.index', compact('user', 'recentOrders'));
    }
}
