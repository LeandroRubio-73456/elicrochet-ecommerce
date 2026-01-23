<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Exclude parent orders (type='standard', status='pending_payment') from recent orders
        // as they are temporary checkout containers
        $recentOrders = $user->orders()
            ->where(function($query) {
                $query->where('type', '!=', 'standard')
                      ->orWhere('status', '!=', \App\Models\Order::STATUS_PENDING_PAYMENT);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('front.account.index', compact('user', 'recentOrders'));
    }
}
