<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Rất quan trọng cho hàng đợi
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue // Đảm bảo CÓ implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Đây là nội dung tùy chỉnh của bạn.
        // Ví dụ:
        return (new MailMessage)
            ->subject('Chào mừng đến với GreenHome - Vui lòng xác minh Email của bạn') // Tiêu đề tùy chỉnh
            ->greeting('Xin chào ' . $notifiable->name . ',') // Lời chào tùy chỉnh
            ->line('Cảm ơn bạn đã đăng ký tài khoản GreenHome! Vui lòng nhấp vào nút bên dưới để xác minh địa chỉ email của bạn và hoàn tất việc đăng ký.')
            ->action('Xác minh địa chỉ Email của tôi', $verificationUrl) // Nút xác minh tùy chỉnh
            ->line('Nếu bạn không đăng ký tài khoản này, bạn có thể bỏ qua email này một cách an toàn.')
            ->salutation('Trân trọng, Đội ngũ GreenHome'); // Lời chào kết thúc tùy chỉnh
            // .line('Liên kết này sẽ hết hạn trong ' . Config::get('auth.verification.expire', 60) . ' phút.'); // Có thể thêm dòng này nếu muốn thông báo thời gian hết hạn
    }

    /**
     * Get the verification URL for the given notifiable.
     * Logic này tạo ra URL xác minh đã ký.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}