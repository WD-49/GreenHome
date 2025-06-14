<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name', // Đã sửa chính tả và đảm bảo fillable
        'product_variant_sku', // Đảm bảo fillable
        'product_attribute', // Đảm bảo fillable
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
        // 'note', // Nếu có cột 'note' trong order_items, thêm vào đây
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed(); // Thêm withTrashed
    }

    public function productVariant()
    {
        // OrderItem liên kết với ProductVariant qua product_variant_id
        return $this->belongsTo(ProductVariant::class)->withTrashed(); // Thêm withTrashed
    }

    // Phương thức truy cập trực tiếp Product từ OrderItem thông qua ProductVariant (optional)
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id', // Khóa chính trên ProductVariant
            'id', // Khóa chính trên Product
            'product_variant_id', // Khóa cục bộ trên OrderItem
            'product_id' // Khóa cục bộ trên ProductVariant
        );
    }
}