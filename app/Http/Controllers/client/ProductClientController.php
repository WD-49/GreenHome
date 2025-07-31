<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\AttributeValue;

use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Client\Request as ClientRequest;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ProductClientController extends Controller
{
    public function show($slug)
    {
        // Lấy sản phẩm kèm các quan hệ cần thiết
        $product = Product::with([
            'brand',
            'category',
            'productVariants.productVariantValues.attributeValue',

            // Bình luận đã được duyệt
            'comments' => function ($query) {
                $query->where('status', 'hiển thị')->latest();
            },
            'comments.user',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Nếu chưa có biến thể thì báo lỗi
        if ($product->productVariants->isEmpty()) {
            abort(404, 'Sản phẩm chưa có biến thể để đánh giá.');
        }

        // Tăng lượt xem
        $product->increment('view');

        // Sản phẩm liên quan
        $relatedProducts = Product::with(['productVariants', 'category', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)
            ->get();

        // Lấy danh sách thuộc tính duy nhất
        $attributes = $product->productVariants
            ->flatMap(function ($variant) {
                return $variant->productVariantValues->pluck('attributeValue.value');
            })
            ->unique()
            ->values();

        return view('client.pages.productDetail', compact('product', 'relatedProducts', 'attributes'));
    }


    public function submitComment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'content'    => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'content'    => $request->content,
            'status'     => 'chưa duyệt',
        ]);

        return redirect()->back()->with('success', 'Bình luận của bạn đã được gửi và đang chờ duyệt.');
    }
    // app/Http/Controllers/VoucherController.php


}
