<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'product_id', 'rating', 'title', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\ReviewFactory::new();
    }
    public function images()
    {
        return $this->hasMany(\App\Models\ReviewImage::class);
    }
}
