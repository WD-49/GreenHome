<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class OrderPaymentNotification extends Notification
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'paid') {
            return [
                'order_id' => $this->order->id,
                'title' => 'Thanh toán đơn hàng #' . $this->order->sku . ' thành công',
                'message' => 'Đơn hàng của bạn đã được thanh toán thành công qua VNPay. Cảm ơn bạn đã mua sắm!',
                'url' => route('orders.show', $this->order->sku),
                'icon' => 'fa fa-check-circle',
                'type' => 'order_payment_success',
            ];
        } elseif ($this->status === 'fail') {
            return [
                'order_id' => $this->order->id,
                'title' => 'Thanh toán đơn hàng #' . $this->order->sku,
                'message' => 'Vui lòng thanh toán đơn hàng của bạn qua VNPay trong vòng 24 giờ để hoàn tất đơn hàng.',
                'url' => route('orders.show', $this->order->sku),
                'icon' => 'fa fa-credit-card',
                'type' => 'order_payment_pending',
            ];
        }

        return []; // Trả về mảng rỗng nếu trạng thái không hợp lệ
    }

    /**
     * Gửi hoặc cập nhật thông báo trong cơ sở dữ liệu.
     */
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
