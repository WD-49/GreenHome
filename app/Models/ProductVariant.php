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
        'attribute_name',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'boolean',
    ];

    // Quan hệ với product (nhiều-1)
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    // Quan hệ với product_variant_values (1-nhiều)
    public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class)->whereNull('deleted_at');
    }
    //quan hệ với user (nhiều-nhiều)
    public function users()
    {
        return $this->belongsToMany(User::class, 'product_variant_user', 'product_variant_id', 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public static function generateUniqueSku(string $productName): string
    {
        do {
            $sku = 'SP' . rand(1000, 9999);
        } while (self::where('sku', $sku)->exists());
        return $sku;
    }


    protected static function booted()
    {
        // Khi tạo mới
        static::created(function ($productVariant) {
            if ($productVariant->product && !$productVariant->product->trashed()) {
                $productVariant->updateProductQuantity();
            }
        });

        // Khi cập nhật
        static::updated(function ($productVariant) {
            if ($productVariant->isDirty('quantity') && $productVariant->product && !$productVariant->product->trashed()) {
                $productVariant->updateProductQuantity();
            }
        });

        // Khi xóa mềm
        static::deleted(function ($productVariant) {
            if ($productVariant->product && !$productVariant->product->trashed()) {
                $productVariant->updateProductQuantity();
            }
        });

        // Khi đang xóa
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

        // Khi khôi phục
        static::restored(function ($productVariant) {
            // Khôi phục cartItems
            $productVariant->cartItems()->onlyTrashed()->each(function ($cartItem) {
                $cartItem->restore();
            });

            // Khôi phục productVariantValues
            $productVariant->productVariantValues()->onlyTrashed()->each(function ($pvv) {
                $pvv->restore();
            });

            // Cập nhật quantity của product
            if ($productVariant->product && !$productVariant->product->trashed()) {
                $productVariant->updateProductQuantity();
            }
        });
    }

    public function updateProductQuantity()
    {
        $product = $this->product;

        if ($product && !$product->trashed()) {
            $total = $product->productVariants()->withoutTrashed()->sum('quantity');
            $product->update(['quantity' => $total]);
        }
    }

    public function scopeFilter($query, $request)
    {
        if ($request->filled('sku')) {
            $query->where('sku', 'LIKE', '%' . $request->sku . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status == 1 ? 1 : 0);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        return $query;
    }
}
