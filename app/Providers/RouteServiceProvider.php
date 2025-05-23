<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // parent::boot();

        // Route::bind('product', function ($value) {
        //     return Product::withTrashed()->where('id', $value)->firstOrFail();
        // });
        // // ✅ Custom binding để lấy ProductVariant cả đã xóa mềm
        // Route::bind('productVariant', function ($value) {
        //     return ProductVariant::withTrashed()->where('id', $value)->findOrFail($value);
        // });
    }
}