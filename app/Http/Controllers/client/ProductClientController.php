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
    // Lấy sản phẩm kèm các quan hệ cần thiết (KHÔNG load comments ở đây nữa)
    $product = Product::with([
        'brand',
        'category',
        'productVariants.productVariantValues.attributeValue',
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

    // Review có phân trang (5 review mỗi trang)
    $reviews = $product->reviews()
        ->with('user')
        ->where('reviews.status', 'approved')
        ->latest()
        ->paginate(5);

    // Comment có phân trang (10 comment mỗi trang)
    $comments = Comment::with('user')
        ->where('product_id', $product->id)
        ->where('status', 'hiển thị')
        ->latest()
        ->paginate(5);

    return view('client.pages.productDetail', compact(
        'product',
        'relatedProducts',
        'attributes',
        'reviews',
        'comments'
    ));
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
