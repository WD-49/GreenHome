<?php

namespace App\Models;

use App\Models\CartItem;
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

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public static function generateUniqueSku(string $productName): string
    {
        do {
            $sku = Str::slug(substr($productName, 0, 5)) . '-' . rand(1000, 9999);
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }


    protected static function booted()
    {
        // Khi tạo mới
        static::created(function ($productVariant) {
            $productVariant->updateProductQuantity();
        });

        // Khi cập nhật
        static::updated(function ($productVariant) {
            // Kiểm tra nếu field 'quantity' có thay đổi
            if ($productVariant->isDirty('quantity')) {
                $productVariant->updateProductQuantity();
            }
        });

        // Khi xóa
        static::deleted(function ($productVariant) {
            $productVariant->updateProductQuantity();
        });
        static::deleting(function ($productVariant) {
            if (!$productVariant->isForceDeleting()) {
                $productVariant->cartItems()->each(function ($cartItem) {
                    $cartItem->delete();
                });
                $productVariant->productVariantValues()->each(function ($pvv) {
                    $pvv->delete();
                });

            }
        });
        static::restored(function ($productVariant) {
            // Khôi phục cartItems đã bị xóa mềm
            $productVariant->cartItems()->onlyTrashed()->each(function ($cartItem) {
                $cartItem->restore();
            });

            // Khôi phục productVariantValues đã bị xóa mềm
            $productVariant->productVariantValues()->onlyTrashed()->each(function ($pvv) {
                $pvv->restore();
            });
        });
    }
    public function updateProductQuantity()
    {
        $product = $this->product;

        if ($product) {
            $total = $product->productVariants()->withoutTrashed()->sum('quantity');
            $product->update(['quantity' => $total]);
        }
    }

}
