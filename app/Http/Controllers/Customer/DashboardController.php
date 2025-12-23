<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        return view('customer.dashboard', compact('user', 'recentOrders'));
    }
}
