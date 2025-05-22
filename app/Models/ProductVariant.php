<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'boolean',
    ];

    // Quan hệ với product (nhiều-1)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ với product_variant_values (1-nhiều)
    public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class);
    }

    protected static function booted()
    {
        // Xử lý khi xóa mềm ProductVariant
        static::deleting(function ($productVariant) {
            if ($productVariant->isSoftDeleting()) {
                // Xóa mềm tất cả product_variant_values liên quan
                $productVariant->productVariantValues()->delete();
            }
        });
    }
}
