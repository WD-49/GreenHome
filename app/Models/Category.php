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
        'status', // thêm status
    ];

    /**
     * Quan hệ: 1 danh mục có nhiều sản phẩm.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Override phương thức boot để xử lý:
     * - Tự sinh slug nếu chưa có.
     */
    protected static function boot()
    {
        parent::boot();

        // Tự tạo slug nếu chưa có
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // *** BỎ đoạn này để KHÔNG xóa mềm sản phẩm khi xóa mềm danh mục ***
        // static::deleting(function ($category) {
        //     if (!$category->isForceDeleting()) {
        //         $category->products()->each(function ($product) {
        //             $product->delete();
        //         });
        //     }
        // });

        // *** BỎ đoạn này để KHÔNG restore sản phẩm khi restore danh mục ***
        // static::restoring(function ($category) {
        //     $category->products()->onlyTrashed()->restore();
        // });
    }
    public function banners()
{
    return $this->hasMany(\App\Models\Banner::class);
}

}
