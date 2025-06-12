<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'sku',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'status_id',
        'discount_id',
        'payment_method_id',
        'payment_status',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'note',
        'cancel_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    public function canBeCancelled()
    {
        // Ví dụ: Đơn hàng không thể hủy nếu đã "Hoàn tất", "Đã giao hàng" hoặc "Đã hủy"
        if ($this->status) { // Đảm bảo $this->status tồn tại và đã được load
            $nonCancellableStatuses = ['Xác nhận', 'Đang vận chuyển', 'Đã hủy', 'Đã giao hàng']; // Các trạng thái không cho phép hủy
            return !in_array($this->status->name, $nonCancellableStatuses);
        }
        return false; // Mặc định không cho hủy nếu không có status hoặc status không được load
    }
}
