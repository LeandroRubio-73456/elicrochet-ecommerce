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

        // Force HTTPS in all environments (Local + Production) to support HTTP/2
        \Illuminate\Support\Facades\URL::forceScheme('https');

        view()->composer('*', function ($view) {
            if (request()->user()) {
                $cartCount = \App\Models\CartItem::where('user_id', request()->user()->id)->sum('quantity');
                $view->with('cartCount', $cartCount);
            } else {
                $view->with('cartCount', 0);
            }
        });
    }
}
