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
        'sort_des',
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

        // Xử lý khi khôi phục Product
        static::restored(function ($product) {
            // Khôi phục tất cả product_variants liên quan
            $product->productVariants()->onlyTrashed()->each(function ($productVariant) {
                $productVariant->restore();
            });

            // Tính lại quantity dựa trên các product_variants chưa bị xóa mềm
            $total = $product->productVariants()->withoutTrashed()->sum('quantity');
            $product->update(['quantity' => $total]);
        });
    }
    public function scopeFilter($query, $request)
    {
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id)
                ->whereHas('brand', fn($q) => $q->whereNull('deleted_at'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == 1 ? 1 : 0);
        }

        if ($request->filled('min_date') && $request->filled('max_date')) {
            $query->whereBetween('date_of_entry', [$request->min_date, $request->max_date]);
        } elseif ($request->filled('min_date')) {
            $query->where('date_of_entry', '>=', $request->min_date);
        } elseif ($request->filled('max_date')) {
            $query->where('date_of_entry', '<=', $request->max_date);
        }

        return $query;
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
