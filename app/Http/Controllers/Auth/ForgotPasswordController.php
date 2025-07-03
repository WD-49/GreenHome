<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendResetPasswordMailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password; // Dùng cho Password::broker()

class ForgotPasswordController extends Controller
{
    // KHÔNG SỬ DỤNG trait SendsPasswordResetEmails nữa

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Handle the request to send a password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        // 1. Validate email
        $request->validate(['email' => 'required|email']);

        // 2. Find the user
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        // 3. Check email verification
        if (is_null($user->email_verified_at)) {
            return back()->withErrors(['email' => 'Email này chưa được xác thực ở hệ thống. Vui lòng xác thực email của bạn trước khi khôi phục mật khẩu.']);
        }

        // 4. Create password reset token
        // Sử dụng broker của Laravel để tạo token và lưu vào bảng password_reset_tokens
        $token = Password::broker()->createToken($user);

        // 5. Dispatch your custom email job
        dispatch(new SendResetPasswordMailJob($user->email, $token));

        return back()->with('status', 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn!');
    }
}