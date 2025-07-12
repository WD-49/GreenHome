<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

/**
 * Notification: Nhắc người dùng xác minh email
 */
class VerifyEmailReminder extends Notification
{
    use Queueable;

    /**
     * Kênh gửi thông báo (chỉ database)
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Dữ liệu lưu vào bảng notifications
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Vui lòng xác minh địa chỉ email của bạn',
            'message' => 'Hãy xác minh email để sử dụng đầy đủ tính năng.',
            'url' => route('profile.index'),
            'icon' => 'fa fa-envelope', // nếu bạn hiển thị bằng FontAwesome
            'type' => 'verify_email',
        ];
    }
}
