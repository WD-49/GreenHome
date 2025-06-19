<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    protected $dates = ['deleted_at'];

    // Quan hệ với sản phẩm (1 brand - nhiều sản phẩm)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
