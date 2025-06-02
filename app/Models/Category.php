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
     * - Xóa mềm sản phẩm khi danh mục bị xóa mềm.
     * - Khôi phục sản phẩm khi khôi phục danh mục.
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

        // Xóa mềm các sản phẩm khi danh mục bị xóa
        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) {
                $category->products()->each(function ($product) {
                    $product->delete();
                });
            }
        });

        // Khôi phục lại các sản phẩm khi danh mục được restore
        static::restoring(function ($category) {
            $category->products()->onlyTrashed()->restore();
        });
    }
}
