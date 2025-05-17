<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['cart_id', 'product_variant_id', 'quantity', 'unit_price', 'total_price', 'note'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
