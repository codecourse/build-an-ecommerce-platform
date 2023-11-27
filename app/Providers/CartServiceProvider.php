<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Cart\Contracts\CartInterface;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CartInterface::class, function () {
            return new Cart(session());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
