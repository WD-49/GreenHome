<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'name',
    ];

    // Quan hệ với attribute_values (1-nhiều)
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    // protected static function booted()
    // {
    //     // Xử lý khi xóa mềm Attribute
    //     static::deleting(function ($attribute) {
    //         if ($attribute->isSoftDeleting()) {
    //             // Xóa mềm tất cả attribute_values liên quan
    //             $attribute->attributeValues()->each(function ($attributeValues) {
    //                 $attributeValues->delete(); // Kích hoạt deleting trong attributeValues
    //             });
    //         }
    //     });
    // }
}
