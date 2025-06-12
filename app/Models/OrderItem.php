<?php

namespace App\Models;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'product_variant_id', 'quantity', 'unit_price', 'total_price', 'poduct_name', 'product_variant_sku']; // Thêm các cột còn thiếu trong $fillable nếu cần

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class)->withTrashed(); // Đảm bảo include soft deleted variants
    }

    // Sửa phương thức product() để trả về mối quan hệ Product thông qua ProductVariant
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,      // Model cuối cùng bạn muốn truy cập (Product)
            ProductVariant::class, // Model trung gian (ProductVariant)
            'id',                // Khóa ngoại trên bảng trung gian (product_variants) trỏ đến id của ProductVariant (đây là khóa chính của ProductVariant)
            'id',                // Khóa chính trên bảng cuối cùng (products)
            'product_variant_id',// Khóa cục bộ trên bảng hiện tại (order_items) trỏ đến ProductVariant
            'product_id'         // Khóa cục bộ trên bảng trung gian (product_variants) trỏ đến Product
        );
    }
}