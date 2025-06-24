<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendResetPasswordMailJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    /**
     * Ghi đè phương thức resetPassword trong trait ResetsPasswords
     */
    protected function resetPassword(User $user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        // Gửi email bằng job đưa vào hàng đợi
        dispatch(new SendResetPasswordMailJob($user->email, $password));

        // Đăng nhập người dùng sau khi reset
        $this->guard()->login($user);
    }
}
