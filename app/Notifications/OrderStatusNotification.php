<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $status)
    {
        $this->order = $order;
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
        $statusMessages = [
            'Chưa xác nhận' => [
                'title' => 'Đơn hàng #' . $this->order->sku . ' đang chờ xử lý',
                'message' => 'Đơn hàng của bạn đã được đặt và đang chờ xác nhận.',
                'icon' => 'fa fa-clock',
                'type' => 'order_pending',
            ],
            'Xác nhận' => [
                'title' => 'Đơn hàng #' . $this->order->sku . ' đã được xác nhận',
                'message' => 'Đơn hàng của bạn đã được xác nhận và đang được xử lý.',
                'icon' => 'fa fa-check-circle',
                'type' => 'order_confirmed',
            ],
            'Đang vận chuyển' => [
                'title' => 'Đơn hàng #' . $this->order->sku . ' đang được giao',
                'message' => 'Đơn hàng của bạn đã được gửi đi. Vui lòng theo dõi trạng thái giao hàng.',
                'icon' => 'fa fa-truck',
                'type' => 'order_shipped',
            ],
            'Giao hàng thành công' => [
                'title' => 'Đơn hàng #' . $this->order->sku . ' đã được giao',
                'message' => 'Đơn hàng của bạn đã được giao thành công. Vui lòng truy cập chi tiết đơn hàng và xác nhận bạn đã nhận hàng.',
                'icon' => 'fa fa-box',
                'type' => 'order_delivered',
            ],
            'Hủy đơn' => [
                'title' => 'Đơn hàng #' . $this->order->sku . ' đã bị hủy',
                'message' => 'Đơn hàng của bạn đã bị hủy. Vui lòng liên hệ để biết thêm chi tiết.',
                'icon' => 'fa fa-times-circle',
                'type' => 'order_cancelled',
            ],
        ];

        $data = $statusMessages[$this->status] ?? [
            'title' => 'Đơn hàng #' . $this->order->id . ' có cập nhật trạng thái',
            'message' => 'Đơn hàng của bạn đã được cập nhật trạng thái.',
            'icon' => 'fa fa-info-circle',
            'type' => 'order_status_updated',
        ];

        $data['order_id'] = $this->order->id;
        $data['url'] = route('orders.show', $this->order->sku);

        return $data;
    }

    public function toDatabase($notifiable)
    {
        // Kiểm tra xem đã có thông báo cho đơn hàng này chưa
        $existingNotification = DB::table('notifications')
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->whereJsonContains('data->order_id', $this->order->id)
            ->first();

        $data = $this->toArray($notifiable);

        if ($existingNotification) {
            // Cập nhật thông báo hiện có
            DB::table('notifications')
                ->where('id', $existingNotification->id)
                ->update([
                    'data' => json_encode($data),
                    'read_at' => null, // Đặt lại thành chưa đọc
                    'updated_at' => now(),
                ]);

            return $existingNotification->id;
        }

        // Tạo thông báo mới nếu chưa có
        return $this->toArray($notifiable);
    }
}
