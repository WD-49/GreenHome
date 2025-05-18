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

    // Quan hệ với products (1-nhiều)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // protected static function booted()
    // {
    //     // Xử lý khi xóa mềm Brand
    //     static::deleting(function ($brand) {
    //         if ($brand->isSoftDeleting()) {
    //             $brand->products()->each(function ($product) {
    //                 $product->delete(); // Kích hoạt deleting trong Product
    //             });
    //         }
    //     });
    // }
}
