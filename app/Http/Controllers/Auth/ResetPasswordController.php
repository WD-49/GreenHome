<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected function resetPassword(User $user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        // Gửi mail đơn giản qua hàng đợi, không tạo file riêng
        Mail::to($user->email)->queue(new class($user, $password) extends Mailable implements ShouldQueue {
            use Queueable, SerializesModels;

            public $user, $password;

            public function __construct($user, $password)
            {
                $this->user = $user;
                $this->password = $password;
            }

            public function build()
            {
                return $this->subject('Mật khẩu mới từ GreenHome')
                            ->text('emails.password_plain'); // dùng file blade text
            }
        });

        $this->guard()->login($user);
    }
}
