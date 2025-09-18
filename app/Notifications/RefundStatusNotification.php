<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RefundStatusNotification extends Notification
{
    use Queueable;

    protected $refund;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($refund, $status)
    {
        $this->refund = $refund;
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
        $statusMessages = [
            'pending' => [
                'title' => 'Yêu cầu hoàn hàng cho đơn #' . $this->refund->order->sku . ' đang chờ xử lý',
                'message' => 'Yêu cầu hoàn hàng của bạn đã được gửi và đang chờ xác nhận.',
                'icon' => 'fa fa-clock',
                'type' => 'refund_pending',
            ],
            'approved' => [
                'title' => 'Yêu cầu hoàn hàng cho đơn #' . $this->refund->order->sku . ' đã được phê duyệt',
                'message' => 'Yêu cầu hoàn hàng của bạn đã được phê duyệt. Vui lòng cung cấp thông tin tài khoản để hoàn tiền.',
                'icon' => 'fa fa-check-circle',
                'type' => 'refund_approved',
            ],
            'rejected' => [
                'title' => 'Yêu cầu hoàn hàng cho đơn #' . $this->refund->order->sku . ' bị từ chối',
                'message' => 'Yêu cầu hoàn hàng của bạn đã bị từ chối. Vui lòng kiểm tra lý do.',
                'icon' => 'fa fa-times-circle',
                'type' => 'refund_rejected',
            ],
            'refund_pending' => [
                'title' => 'Yêu cầu hoàn tiền cho đơn #' . $this->refund->order->sku . ' đang chờ xử lý',
                'message' => 'Yêu cầu hoàn tiền của bạn đang được xử lý. Vui lòng chờ thông báo.',
                'icon' => 'fa fa-clock',
                'type' => 'refund_processing',
            ],
            'refunded' => [
                'title' => 'Hoàn tiền cho đơn #' . $this->refund->order->sku . ' đã hoàn tất',
                'message' => 'Hoàn tiền cho yêu cầu của bạn đã được thực hiện. Vui lòng kiểm tra tài khoản.',
                'icon' => 'fa fa-check-circle',
                'type' => 'refund_completed',
            ],
        ];

        $data = $statusMessages[$this->status] ?? [
            'title' => 'Yêu cầu hoàn hàng cho đơn #' . $this->refund->order->sku . ' có cập nhật trạng thái',
            'message' => 'Yêu cầu hoàn hàng của bạn đã được cập nhật trạng thái.',
            'icon' => 'fa fa-info-circle',
            'type' => 'refund_status_updated',
        ];

        $data['refund_id'] = $this->refund->id;
        $data['url'] = route('orders.show', $this->refund->order->sku);  // Hoặc route chi tiết hoàn hàng nếu có

        return $data;
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
