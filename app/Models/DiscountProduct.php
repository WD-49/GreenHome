<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountProduct extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['discount_id', 'product_id'];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function discountProduct()
    {
        return $this->belongsTo(Product::class);
    }
}
