<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'discount_id',
        'payment_method_id',
        'discount_code', // Đảm bảo fillable
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

    public function discount()
    {
        return $this->belongsTo(Discount::class)->withTrashed(); // Thêm withTrashed
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class)->withTrashed(); // Thêm withTrashed
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
}