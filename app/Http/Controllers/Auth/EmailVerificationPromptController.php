<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter; // Import RateLimiter
use Illuminate\Validation\ValidationException; // Import ValidationException


class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home', absolute: false)) // Chuyển hướng về trang chủ hoặc dashboard của bạn
                    : view('auth.verify-email'); // Đảm bảo view này tồn tại
    }

    /**
     * Send a new email verification notification.
     */
    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        // Kiểm tra giới hạn tần suất gửi
        $key = 'send_verification_email:' . $request->user()->id;
        $decayMinutes = 1; // Giới hạn 1 phút
        $maxAttempts = 6;  // Tối đa 6 lần thử

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => __('Bạn đã gửi quá nhiều yêu cầu xác minh email. Vui lòng thử lại sau :seconds giây.', [
                    'seconds' => $seconds,
                ]),
            ])->redirectTo(route('verification.notice')); // Redirect về trang thông báo xác minh
        }

        RateLimiter::hit($key, $decayMinutes * 60); // Đánh dấu đã gửi và đặt thời gian hết hạn

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false)); // Đã xác minh, chuyển hướng về trang chủ hoặc dashboard
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}