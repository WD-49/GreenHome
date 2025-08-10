<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_name', // Đảm bảo fillable nếu bạn lưu tên người dùng trực tiếp
        'sku',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'order_status', // Use 'order_status' directly
        'discount_code', // Đảm bảo fillable
        'discount_type',
        'discount_value', // Đảm bảo fillable
        'payment_method_name', // Đảm bảo fillable
        'payment_status',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'note',
        'cancel_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed(); // Thêm withTrashed nếu user có thể bị soft delete
    }



    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }



    // Phương thức kiểm tra xem đơn hàng có thể bị hủy không
    public function canBeCancelled(): bool
    {
        // Các trạng thái mà đơn hàng KHÔNG THỂ bị hủy
        // Ví dụ: Đơn hàng đã "Đang vận chuyển", "Giao hàng thành công", hoặc đã "Hủy đơn" thì không thể hủy nữa.
        // Nếu bạn muốn cho phép hủy ở trạng thái "Xác nhận", hãy bỏ "Xác nhận" khỏi mảng này.
        $nonCancellableStatuses = ['Đang vận chuyển', 'Giao hàng thành công', 'Hủy đơn'];

        return !in_array($this->order_status, $nonCancellableStatuses);
    }

    public function canBeCancel(): bool
    {
        $nonCancellableStatuses = ['Đang vận chuyển', 'Giao hàng thành công', 'Hủy đơn']; // Phân tích trạng thái không thể hủy

        return !in_array($this->order_status, $nonCancellableStatuses)
            && $this->payment_status !== 'paid';
    }

    protected static function booted()
    {
        static::updated(function ($order) {
            // Nếu trạng thái đơn hàng là "Giao hàng thành công" thì cập nhật trạng thái thanh toán
            if (
                $order->order_status === 'Giao hàng thành công' &&
                $order->payment_status !== 'paid'
            ) {
                $order->payment_status = 'paid';
                $order->saveQuietly(); // dùng saveQuietly để tránh vòng lặp sự kiện
            }

            // Thêm trường hợp mới: nếu trạng thái là "Giao hàng thành công",
            // tự động chuyển sang "Đã nhận hàng"
            if ($order->order_status === 'Giao hàng thành công') {
                $order->order_status = 'Đã nhận hàng';
                $order->saveQuietly();
            }
        });
    }


    public static function generateUniqueSku()
    {
        do {
            // Sinh số ngẫu nhiên từ 100 đến 100000
            $randomNumber = mt_rand(100, 100000);

            // Tạo mã SKU theo format DH + số ngẫu nhiên có 6 chữ số (bổ sung 0 nếu cần)
            $sku = 'DH' . str_pad($randomNumber, 6, '0', STR_PAD_LEFT);

            // Kiểm tra đã tồn tại SKU này chưa
            $exists = self::where('sku', $sku)->exists();
        } while ($exists); // Nếu tồn tại thì sinh lại

        return $sku;
    }
}
