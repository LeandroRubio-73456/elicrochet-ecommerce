<?php

namespace App\Providers;

use App\Http\View\Composers\AdminNotificationComposer;
use App\Http\View\Composers\UserNotificationComposer;
use App\Models\CartItem;
use App\Models\Review;
use App\Observers\ReviewObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Force HTTPS in Production only
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        Review::observe(ReviewObserver::class);

        view()->composer('*', function ($view) {
            if (request()->user()) {
                $cartCount = CartItem::where('user_id', request()->user()->id)->sum('quantity');
                $view->with('cartCount', $cartCount);
            } else {
                $view->with('cartCount', 0);
            }
        });

        // Admin Notifications
        view()->composer(['layouts.layout-vertical', 'admin.*'], AdminNotificationComposer::class);

        // User Notifications
        view()->composer(['layouts.front-layout', 'front.partials.navbar'], UserNotificationComposer::class);
    }
}
