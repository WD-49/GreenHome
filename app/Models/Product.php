<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'slug',
        'quantity',
        'date_of_entry',
        'status',
        'image',
        'view',
    ];

    protected $casts = [
        'date_of_entry' => 'datetime',
        'status' => 'boolean',
        'quantity' => 'integer',
        'view' => 'integer',
    ];

    // Quan hệ với category (nhiều-1)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_products');
    }


    // Quan hệ với brand (nhiều-1)
    public function brand()
    {
        return $this->belongsTo(Brand::class)->withTrashed(); // xóa mềm giữ sản phẩm
    }


    // Quan hệ với product_variants (1-nhiều)
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class)->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }




    public function getReviewsAvgRatingAttribute()
    {
        $variants = $this->productVariants;

        if ($variants->isEmpty()) {
            return null;
        }

        $total = 0;
        $count = 0;

        foreach ($variants as $variant) {
            foreach ($variant->reviews as $review) {
                $total += $review->rating;
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 1) : null;
    }

    public function getReviewsCountAttribute()
    {
        return $this->productVariants->flatMap(function ($variant) {
            return $variant->reviews;
        })->count();
    }


    protected static function booted()
    {
        // Xử lý khi xóa mềm Product
        static::deleting(function ($product) {
            if (!$product->isForceDeleting()) {
                // Xóa mềm tất cả product_variants liên quan
                $product->productVariants()->each(function ($productVariants) {
                    $productVariants->delete();
                });
            }
        });
    }
    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            ProductVariant::class,
            'product_id', // Foreign key trên bảng product_variants
            'product_variant_id', // Foreign key trên bảng reviews  
            'id', // Local key trên bảng products
            'id' // Local key trên bảng product_variants
        );
    }

    public function wishlists()
{
    return $this->hasMany(WishList::class);
}

    
}
