<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;

class ProductClientController extends Controller
{
    public function show($slug)
    {
        $product = Product::with([
            'brand',
            'category',
            'productVariants.productVariantValues.attributeValue',
            'comments.user',
        ])->where('slug', $slug)->firstOrFail();

        $product->increment('view');

        $relatedProducts = Product::with(['productVariants', 'category', 'comments'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)
            ->get();

        $attributes = $product->productVariants
            ->flatMap(function ($variant) {
                return $variant->productVariantValues->pluck('attributeValue.value');
            })
            ->unique()
            ->values();

        // ✅ Lấy các review dựa vào product_variant_id
        $variantIds = $product->productVariants->pluck('id');
        $reviews = Review::with([
            'user',
            'productVariant.productVariantValues.attributeValue',
        ])
            ->whereIn('product_variant_id', $variantIds)
            ->get();


        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
        $totalStar = $reviews->sum('rating');

        return view('client.pages.productDetail', compact(
            'product',
            'relatedProducts',
            'attributes',
            'reviews',
            'totalReviews',
            'avgRating',
            'totalStar'
        ));
    }
}
