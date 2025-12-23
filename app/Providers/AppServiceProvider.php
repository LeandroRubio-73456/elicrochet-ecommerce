<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
