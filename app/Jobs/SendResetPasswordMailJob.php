<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail; // <-- Đảm bảo đã import đúng Mailable của bạn

class SendResetPasswordMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $token;

    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    public function handle()
    {
        // Xây dựng URL đặt lại mật khẩu với token
        $resetUrl = route('password.reset', ['token' => $this->token, 'email' => $this->email]);

        // Gửi email bằng Mailable của bạn
        Mail::to($this->email)->send(new ResetPasswordMail($this->email, $resetUrl));
    }
}