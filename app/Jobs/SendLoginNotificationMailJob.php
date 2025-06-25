<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class SendLoginNotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $name;
    protected $time;

    public function __construct(string $email, string $name, string $time)
    {
        $this->email = $email;
        $this->name = $name;
        $this->time = $time;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new class($this->name, $this->time) extends Mailable {
            public $name, $time;

            public function __construct($name, $time)
            {
                $this->name = $name;
                $this->time = $time;
            }

            public function build()
            {
                return $this->subject('Thông báo đăng nhập từ GreenHome')
                    ->view('emails.welcome')
                    ->with([
                        'name' => $this->name,
                        'time' => $this->time,
                    ]);
            }
        });
    }
}

