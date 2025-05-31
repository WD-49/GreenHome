<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    // Quan hệ với products (1-nhiều)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Khôi phục danh mục sẽ khôi phục các sản phẩm đã xóa mềm trong danh mục
    protected static function booted()
    {
        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) {
                // Xóa mềm tất cả category_variants liên quan
                $category->products()->each(function ($products) {
                    $products->delete();
                });
            }
        });

        static::restoring(function ($category) {
            $category->products()->onlyTrashed()->restore();
        });

    }
     public static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

}