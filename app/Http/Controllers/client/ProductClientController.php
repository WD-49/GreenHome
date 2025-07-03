<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\AttributeValue;

class ProductClientController extends Controller
{
    public function show($slug)
    {
        // Lấy sản phẩm, kèm các quan hệ cần thiết
        $product = Product::with([
            'brand',
            'category',
            'productVariants.productVariantValues.attributeValue',
            'comments.user',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // dd($product);

        // Tăng lượt xem
        $product->increment('view');

        // Lấy sản phẩm cùng danh mục (trừ sản phẩm hiện tại)
        $relatedProducts = Product::with(['productVariants', 'category', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)
            ->get();

        // Xử lý danh sách thuộc tính của sản phẩm
        $attributes = $product->productVariants
            ->flatMap(function ($variant) {
                return $variant->productVariantValues->pluck('attributeValue.value');
            })
            ->unique()
            ->values();


        $reviews = $product->reviews()->with('user')->get();


        return view('client.pages.productDetail', compact('product', 'relatedProducts', 'attributes', 'reviews'));
    }
}
