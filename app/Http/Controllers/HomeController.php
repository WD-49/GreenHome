<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerifyEmailReminder;

class HomeController extends Controller
{
    /**
     * Yêu cầu đăng nhập
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Trang chính sau khi đăng nhập
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Gửi thông báo nếu chưa xác thực và chưa từng nhận thông báo này
        if ($user && !$user->hasVerifiedEmail()) {
            $alreadyNotified = $user->unreadNotifications()
                ->where('type', VerifyEmailReminder::class)
                ->exists();

            if (!$alreadyNotified) {
                $user->notify(new VerifyEmailReminder());
            }
        }


        // Lấy thông báo để truyền vào view
        $notifications = $user->unreadNotifications;

        return view('home', compact('notifications'));
    }
}
