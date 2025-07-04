<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\attributeValue;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;

class DiscountController extends Controller
{
    // VoucherController.php




    public function showEligibleProducts(Request $request, $code)
{
    $voucher = Discount::where('code', $code)->firstOrFail();

    $products = Product::query()
        ->whereHas('discounts', fn($q) => $q->where('code', $code))
        ->with(['brand', 'productVariants', 'reviews'])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating');

    // Lọc theo danh mục
   $categoryId = $request->input('category_id');
if (!empty($categoryId)) {
    $products->where('category_id', $categoryId);
}

    // Lọc theo thương hiệu
    $brandIds = $request->input('brand_id', []);
    if (!empty($brandIds)) {
        $products->whereIn('brand_id', (array) $brandIds);
    }

    // Lọc theo biến thể (attribute_values)
    if ($request->filled('attribute_values')) {
        $products->whereHas('productVariants.attributeValues', function ($q) use ($request) {
            $q->whereIn('attribute_value_id', $request->attribute_values);
        });
    }

    // Lọc theo đánh giá
    if ($request->filled('rating')) {
        $products->having('reviews_avg_rating', '>=', (int) $request->rating);
    }

    // Lọc theo giá
    if ($request->filled('min_price') || $request->filled('max_price')) {
        $products->whereHas('productVariants', function ($q) use ($request) {
            if ($request->filled('min_price')) {
                $q->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $q->where('price', '<=', $request->max_price);
            }
        });
    }

    // Sắp xếp
    if ($request->filled('sort')) {
        match ($request->sort) {
            'latest' => $products->latest(),
            'oldest' => $products->oldest(),
         'hot' => $products->orderByDesc('created_at'), // hoặc orderBy('reviews_avg_rating', 'desc')

            default  => null,
        };
    }

    $products = $products->paginate(12);

    $categories = Category::withCount('products')->get();
    $brands = Brand::withCount('products')->get();
    $attributeValues = AttributeValue::with('attribute')->get();

    return view('client.pages.voucher', compact(
        'voucher',
        'products',
        'categories',
        'brands',
        'attributeValues'
    ));
}



    public function showDetail($code)
    {
        $voucher = Discount::where('code', $code)
            ->where('status', 'active') // Chỉ lấy voucher đang hoạt động
            ->first();

        if (!$voucher) {
            abort(404, 'Voucher không tồn tại hoặc đã hết hiệu lực');
        }

        return view('client.pages.voucherDetail', compact('voucher'));
    }
}
