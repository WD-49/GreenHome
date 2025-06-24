<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $password;

    /**
     * Tạo Job mới.
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Thực thi gửi email.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new class($this->password) extends Mailable {
            public $password;

            public function __construct($password)
            {
                $this->password = $password;
            }

            public function build()
            {
                return $this->subject('Mật khẩu mới từ GreenHome')
                    ->view('emails.password_plain')
                    ->with([
                        'password' => $this->password,
                    ]);
            }
        });
    }
}
