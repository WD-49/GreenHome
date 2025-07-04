<?php

namespace App\Providers;

use App\Models\Discount;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();
       View::composer('*', function ($view) {
    $vouchers = Discount::where('end_date', '>=', now())
                        ->where('status', 'active')
                        ->get();

    $view->with('vouchers', $vouchers);
});

    }
}
