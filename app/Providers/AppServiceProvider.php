<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \App\Models\Review::observe(\App\Observers\ReviewObserver::class);

        view()->composer('*', function ($view) {
            if (request()->user()) {
                $cartCount = \App\Models\CartItem::where('user_id', request()->user()->id)->sum('quantity');
                $view->with('cartCount', $cartCount);
            } else {
                $view->with('cartCount', 0);
            }
        });

        // Admin Notifications
        view()->composer(['layouts.layout-vertical', 'admin.*'], \App\Http\View\Composers\AdminNotificationComposer::class);

        // User Notifications
        view()->composer(['layouts.front-layout', 'front.partials.navbar'], \App\Http\View\Composers\UserNotificationComposer::class);
    }
}
