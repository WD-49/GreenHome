<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'name',
        'description',

    ];
    protected $dates = ['deleted_at'];

    // Quan hệ với products (1-nhiều)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

 protected static function booted()
    {
        // Khi xóa mềm brand → xóa mềm các sản phẩm
        static::deleting(function ($brand) {
            // Chỉ xử lý nếu là soft delete, không phải force delete
            if (method_exists($brand, 'trashed') && !$brand->trashed()) {
                $brand->products()->each(function ($product) {
                    $product->delete(); // soft delete
                });
            }
        });

        // Khi khôi phục brand → khôi phục sản phẩm
        static::restoring(function ($brand) {
            $brand->products()->onlyTrashed()->each(function ($product) {
                $product->restore();
            });
        });
    }

}
