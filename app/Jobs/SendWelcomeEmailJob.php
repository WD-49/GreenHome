<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $name;

    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new class($this->name) extends Mailable {
            public $name;

            public function __construct($name)
            {
                $this->name = $name;
            }

            public function build()
            {
                return $this->subject('Chào mừng bạn đến với GreenHome!')
                    ->view('emails.welcome')
                    ->with(['name' => $this->name]);
            }
        });
    }
}
