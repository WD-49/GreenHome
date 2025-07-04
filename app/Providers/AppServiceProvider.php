<?php

namespace App\Providers;

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

        // Đăng ký View Composer
        View::composer('*', HeaderComposer::class);
        View::composer('*', FooterComposer::class);
    }
}
