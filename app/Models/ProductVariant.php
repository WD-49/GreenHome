<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
        'image',
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
    public static function generateUniqueSku(string $productName): string
    {
        do {
            $sku = Str::slug(substr($productName, 0, 5)) . '-' . rand(1000, 9999);
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }


    // protected static function booted()
    // {
    //     // Xử lý khi xóa mềm ProductVariant
    //     static::deleting(function ($productVariant) {
    //         if ($productVariant->isSoftDeleting()) {
    //             // Xóa mềm tất cả product_variant_values liên quan
    //             $productVariant->productVariantValues()->delete();
    //         }
    //     });
    // }
}
