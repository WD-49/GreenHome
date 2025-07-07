<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail as LaravelVerifyEmail; // Import Notification mặc định của Laravel
use App\Notifications\VerifyEmail as CustomVerifyEmail; // Import Notification tùy chỉnh của bạn

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Ví dụ về policy mapping
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Đăng ký các chính sách authorization (nếu bạn có)
        $this->registerPolicies();

        // *** Ghi đè Notification xác minh email mặc định của Laravel ***
        // Bind (ghi đè) Notification mặc định của Laravel bằng Notification tùy chỉnh của bạn
        $this->app->bind(
            LaravelVerifyEmail::class, // Notification mặc định của Laravel
            CustomVerifyEmail::class   // Notification tùy chỉnh của bạn (App\Notifications\VerifyEmail)
        );
    }
}