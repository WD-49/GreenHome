<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WishList extends Model
{
    /** @use HasFactory<\Database\Factories\WishListFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'product_id', 'add_at', 'notify_on_sale', 'priority'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
