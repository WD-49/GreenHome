<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class attributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'attribute_id',
        'value',
    ];

    // Quan hệ với attribute (nhiều-1)
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // Quan hệ với product_variant_values (1-nhiều)
    public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class);
    }

    // protected static function booted()
    // {
    //     // Xử lý khi xóa mềm AttributeValue
    //     static::deleting(function ($attributeValue) {
    //         if ($attributeValue->isSoftDeleting()) {
    //             // Xóa mềm tất cả product_variant_values liên quan
    //             $attributeValue->productVariantValues()->delete();
    //         }
    //     });
    // }
}
