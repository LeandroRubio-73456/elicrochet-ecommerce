<?php

namespace App\Http\View\Composers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $userNotifications = Order::where('user_id', Auth::id())
                ->whereIn('status', [
                    Order::STATUS_PENDING_PAYMENT,
                    Order::STATUS_WORKING,
                    Order::STATUS_SHIPPED,
                ])
                ->latest()
                ->take(5)
                ->get();

            $userNotificationCount = $userNotifications->count();

            $view->with([
                'userNotifications' => $userNotifications,
                'userNotificationCount' => $userNotificationCount,
            ]);
        }
    }
}
