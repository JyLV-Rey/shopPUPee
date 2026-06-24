<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        // Pass cart count and total to the navbar on every page
        View::composer('common.navbar', function ($view) {
            if (Auth::check()) {
                $cartItems = CartItem::with('product')
                    ->where('buyer_id', Auth::user()->buyer_id)
                    ->get();

                $navCartTotal = 0;
                foreach ($cartItems as $item) {
                    $navCartTotal += $item->product->price * $item->quantity;
                }

                $view->with('navCartCount', $cartItems->sum('quantity'));
                $view->with('navCartTotal', $navCartTotal);
            } else {
                $view->with('navCartCount', 0);
                $view->with('navCartTotal', 0);
            }
        });
    }
}
