<?php

namespace App\Http\Controllers\Client;

use App\Models\Review;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Category;

use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\AttributeValue;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\Request as ClientRequest;

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
            ->paginate(2);

        // Comment có phân trang (2 comment mỗi trang)
        $comments = Comment::with('user')
            ->where('product_id', $product->id)
            ->where('status', 'hiển thị')
            ->latest()
            ->paginate(2);

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
    public function getProductDetails($id)
    {
        try {
            $product = Product::with(['productVariants', 'category', 'brand'])->findOrFail($id);

            // Tính số sao trung bình từ reviews của tất cả biến thể
            $averageRating = DB::table('reviews')
                ->whereIn('product_variant_id', $product->productVariants->pluck('id'))
                ->avg('rating') ?? 0;

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'sortDes' => $product->sort_des,
                    'average_rating' => round($averageRating, 1), // Làm tròn đến 1 chữ số thập phân
                    'review_count' => DB::table('reviews')
                        ->whereIn('product_variant_id', $product->productVariants->pluck('id'))
                        ->count(),
                    'view' => $product->view ?? 0,
                    'category' => $product->category ? ['name' => $product->category->name] : null,
                    'brand' => $product->brand ? ['name' => $product->brand->name] : null,
                    'product_variants' => $product->productVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'price' => $variant->price,
                            'old_price' => $variant->old_price,
                            'attribute_name' => $variant->attribute_name ?? '',
                            'quantity' => $variant->quantity,
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin sản phẩm.',
            ], 500);
        }
    }
}
