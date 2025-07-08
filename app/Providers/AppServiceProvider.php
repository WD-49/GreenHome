<?php

namespace App\Providers;

use App\Models\Discount;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\View\Composers\HeaderComposer;
use App\Http\View\Composers\FooterComposer;

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
        // Dùng Bootstrap cho phân trang
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            $vouchers = Discount::where('end_date', '>=', now())
                ->where('status', 'active')
                ->get();

            $view->with('vouchers', $vouchers);
        });


        // Đăng ký View Composer
        View::composer('*', HeaderComposer::class);
        View::composer('*', FooterComposer::class);
    }
}
