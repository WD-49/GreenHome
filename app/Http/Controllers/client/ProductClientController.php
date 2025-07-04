<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;


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
    if ($product->productVariants->isEmpty()) {
        abort(404, 'Sản phẩm chưa có biến thể để đánh giá.');
    }
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
public function submitReview(Request $request)
{
    $request->validate([
        'product_variant_id' => 'required|exists:product_variants,id',
        'rating' => 'required|integer|min:1|max:5',
        'title' => 'required|string|max:150',
        'content' => 'required|string|max:1000',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
    ]);

    $review = Review::create([
        'user_id' => Auth::id(),
        'product_variant_id' => $request->product_variant_id,
        'rating' => $request->rating,
        'title' => $request->title,
        'content' => $request->content,
        'status' => 'approved',
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('review_images', 'public');

            $review->images()->create([
                'image' => $path,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi.');
}


    // Xử lý gửi bình luận
    public function submitComment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Bình luận đã được gửi.');
    }
}
