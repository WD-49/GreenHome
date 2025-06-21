<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductClientController extends Controller
{
    public function show($slug)
    {
        // Lấy sản phẩm theo slug, kèm các quan hệ cần thiết
        $product = Product::with(['brand', 'category', 'productVariants', 'comments'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Tăng view mỗi lần xem chi tiết sản phẩm
        $product->increment('view');

        // Lấy 6 sản phẩm phổ biến nhất theo lượt xem, kèm biến thể
        $popularProducts = Product::with('productVariants')
            ->orderByDesc('view')
            ->take(6)
            ->get();

        // Trả về view chi tiết sản phẩm
        return view('client.pages.productDetail', compact('product', 'popularProducts'));
    }
}
