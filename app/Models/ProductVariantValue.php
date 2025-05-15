<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantValue extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantValueFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
    ];

    // Quan hệ với product_variant (nhiều-1)
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Quan hệ với attribute_value (nhiều-1)
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
