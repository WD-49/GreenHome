<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    // Quan hệ với products (1-nhiều)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Khôi phục danh mục sẽ khôi phục các sản phẩm đã xóa mềm trong danh mục
    protected static function booted()
    {
        static::restoring(function ($category) {
            $category->products()->onlyTrashed()->restore();
        });
    }
    
}