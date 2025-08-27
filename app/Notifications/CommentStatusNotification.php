<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentStatusNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $status)
    {
        $this->comment = $comment;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'approved') {
            return [
                'title' => 'Bình luận đã được duyệt',
                'message' => 'Bình luận của bạn với nội dung "' . $this->comment->content . '" trên sản phẩm "' . $this->comment->product->name . '" đã được duyệt.',
                'url' => route('productDetail', $this->comment->product->slug),
                'icon' => 'fa fa-check-circle',
                'type' => 'comment_approved',
            ];
        } elseif ($this->status === 'hidden') {
            return [
                'title' => 'Bình luận đã bị ẩn',
                'message' => 'Bình luận của bạn với nội dung "' . $this->comment->content . '" trên sản phẩm "' . $this->comment->product->name . '" đã bị ẩn.',
                'url' => route('productDetail', $this->comment->product->slug),
                'type' => 'comment_hidden',
            ];
        }

        return []; // Trả về mảng rỗng nếu trạng thái không hợp lệ
    }
}
