<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
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
  
    protected static function booted()
{
    static::deleting(function ($category) {
        if (! $category->isForceDeleting()) {
            $category->products()->each(function ($product) {
                $product->delete();
            });
        }
    });
}
}
