<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'price',
        'promotional_price',
        'quantity',
        'date_of_entry',
        'status',
        'image',
        'view',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
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

    // Quan hệ với brand (nhiều-1)
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Quan hệ với product_variants (1-nhiều)
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    protected static function booted()
    {
        // Xử lý khi xóa mềm Product
        static::deleting(function ($product) {
            if (! $product->isForceDeleting()) {
                // Xóa mềm tất cả product_variants liên quan
                $product->productVariants()->each(function ($productVariants) {
                    $productVariants->delete();
                });
            }
        });
    }
}
