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
        $nonCancellableStatuses = ['Đang vận chuyển', 'Giao hàng thành công', 'Hủy đơn', 'Đã nhận hàng'];

        return !in_array($this->order_status, $nonCancellableStatuses)
            && $this->payment_status !== 'paid';
    }

    public function canBePay(): bool
    {
        return $this->payment_method_name === 'VNPAY'
            && $this->payment_status === 'pending'
            && $this->order_status !== 'Hủy đơn'
            && $this->order_status !== 'Chưa xác nhận';
    }

    protected static function booted()
    {
        static::updated(function ($order) {
            if (
                ($order->order_status === 'Giao hàng thành công' || $order->order_status === 'Đã nhận hàng')  &&
                $order->payment_status !== 'paid'
            ) {
                $order->payment_status = 'paid';
                $order->saveQuietly(); // dùng saveQuietly để tránh vòng lặp sự kiện
            }
        });
    }


    public static function generateUniqueSku()
    {
        do {
            $randomNumber = mt_rand(100, 100000);

            $sku = 'DH' . str_pad($randomNumber, 6, '0', STR_PAD_LEFT);

            // Kiểm tra đã tồn tại SKU này chưa
            $exists = self::where('sku', $sku)->exists();
        } while ($exists); // Nếu tồn tại thì sinh lại

        return $sku;
    }
}
