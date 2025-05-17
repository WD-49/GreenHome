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

    protected static function booted()
{
    static::deleting(function ($attribute) {
        // Kiểm tra đây là xóa mềm không
        if (! $attribute->forceDeleting) {
            $attribute->attributeValues->each(function ($attributeValue) {
                $attributeValue->delete();
            });
        }
    });
}
}
