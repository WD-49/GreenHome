<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountUsage extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountUsageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'id','discount_id', 'user_id', 'product_id', 'discount_code', 'user_name', 'order_id',  'used_at',  ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

}
