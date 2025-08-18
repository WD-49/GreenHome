<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewStatusNotification extends Notification
{
    use Queueable;

    protected $review;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($review, $status)
    {
        $this->review = $review;
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
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'approved') {
            return [
                'title' => 'Đánh giá đã được duyệt',
                'message' => 'Đánh giá của bạn trên sản phẩm "' . $this->review->productVariant->product->name . '" đã được duyệt.',
                'url' => route('productDetail', $this->review->productVariant->product->slug),
                'icon' => 'fa fa-check-circle',
                'type' => 'review_approved',
            ];
        } elseif ($this->status === 'rejected') {
            return [
                'title' => 'Đánh giá đã bị ẩn',
                'message' => 'Đánh giá của bạn trên sản phẩm "' . $this->review->productVariant->product->name . '" đã bị ẩn.',
                'url' => route('productDetail', $this->review->productVariant->product->slug),
                'type' => 'review_hidden',
            ];
        }

        return []; // Trả về mảng rỗng nếu trạng thái không hợp lệ
    }
}
