<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Có thể thêm nếu bạn muốn Mailable này được queue trực tiếp
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable // Có thể thêm "implements ShouldQueue" nếu bạn muốn email này được đưa vào queue
{
    use Queueable, SerializesModels; // Sử dụng Queueable nếu Mailable này implements ShouldQueue

    public $userEmail;
    public $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param string $userEmail Địa chỉ email của người dùng
     * @param string $resetUrl URL để người dùng đặt lại mật khẩu
     */
    public function __construct(string $userEmail, string $resetUrl)
    {
        $this->userEmail = $userEmail;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yêu cầu đặt lại mật khẩu của bạn', // Tiêu đề email
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password_reset', // Tên view email của bạn
            with: [
                'email' => $this->userEmail,
                'resetUrl' => $this->resetUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}