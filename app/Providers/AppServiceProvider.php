<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Discount;

use App\Http\View\Composers\HeaderComposer;
use App\Http\View\Composers\FooterComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Sử dụng Bootstrap cho phân trang
        Paginator::useBootstrap();

        // Truyền dữ liệu voucher và notifications vào tất cả view
        View::composer('*', function ($view) {
            $vouchers = Discount::where('end_date', '>=', now())
                                ->where('status', 'active')
                                ->get();

            $notifications = Auth::check()
                ? Auth::user()->unreadNotifications
                : collect();

            $view->with([
                'vouchers' => $vouchers,
                'notifications' => $notifications,
            ]);
        });

        // Đăng ký Composer cho header & footer (nếu có logic riêng)
        View::composer('client.partials.header', HeaderComposer::class);

        View::composer('*', FooterComposer::class);
    }
}
