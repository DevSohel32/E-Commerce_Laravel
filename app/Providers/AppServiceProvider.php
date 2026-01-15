<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Surfsidemedia\Shoppingcart\Facades\Cart;


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
        // Use '*' to share with all views, or 'layouts.app' to target specific ones
        View::composer('*', function ($view) {
            $cart = Cart::instance('cart');
            $view->with('cartCount', $cart->content()->count());
        });
    }
}
