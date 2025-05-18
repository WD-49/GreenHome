<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'code',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'max_discount',
        'min_order_value',
        'quantity',
        'user_usage_limit',
        'applies_to_all_products',
        'status',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_products')
            ->withTimestamps()
            ->withTrashed();
    }

    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
