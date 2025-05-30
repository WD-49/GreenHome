<?php

namespace App\Models;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'product_variant_id', 'quantity', 'unit_price', 'total_price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
