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

    // Khởi tạo query
    $products = Product::query()
    ->where(function ($query) use ($voucher) {
        if ($voucher->applies_to_all_products) {
            $query->whereNull('deleted_at'); // tất cả sp chưa bị xóa
        } else {
            $query->whereHas('discounts', function ($q) use ($voucher) {
                $q->where('discounts.id', $voucher->id);
            });
        }
    });


    // Tìm kiếm
   if ($request->filled('search')) {
    $search = $request->input('search');
    $products->where('name', 'like', "%$search%");
}


    // Lọc theo danh mục
    if ($request->filled('category_id')) {
        $products->where('category_id', $request->input('category_id'));
    }

    // Lọc theo thương hiệu
    if ($request->filled('brand_id')) {
        $products->where('brand_id', $request->input('brand_id'));
    }

    // Lọc theo đánh giá (rating)
    if ($request->filled('rating')) {
        $rating = (int) $request->input('rating');
        $products->whereIn('id', function ($sub) use ($rating) {
            $sub->select('products.id')
                ->from('products')
                ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->join('reviews', 'product_variants.id', '=', 'reviews.product_variant_id')
                ->whereNull('reviews.deleted_at')
                ->groupBy('products.id')
                ->havingRaw('AVG(reviews.rating) >= ?', [$rating])
                ->havingRaw('AVG(reviews.rating) < ?', [$rating + 1]);
        });
    }

    // Lọc theo biến thể (attribute_values[])
    if ($request->filled('attribute_values')) {
        $attributeValueIds = collect($request->input('attribute_values'))->map(fn($id) => (int) $id);
        $attributeValues = AttributeValue::with('attribute')->whereIn('id', $attributeValueIds)->get();
        $grouped = $attributeValues->groupBy(fn($v) => $v->attribute->id);

        foreach ($grouped as $attributeId => $values) {
            $valueIds = $values->pluck('id')->toArray();
            $products->whereHas('productVariants.productVariantValues', function ($q) use ($valueIds) {
                $q->whereIn('attribute_value_id', $valueIds);
            });
        }
    }

    // Lọc theo giá
    $min = $request->input('min_price');
    $max = $request->input('max_price');
    if ($min !== null || $max !== null) {
        $min = is_numeric($min) ? (float) $min : 0;
        $max = is_numeric($max) ? (float) $max : 999999999;
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }
        $products->whereHas('productVariants', function ($q) use ($min, $max) {
            $q->whereBetween('price', [$min, $max]);
        });
    }

    // Sắp xếp
    if ($request->filled('sort')) {
        match ($request->sort) {
            'latest' => $products->latest(),
            'oldest' => $products->oldest(),
            'hot'    => $products->orderByDesc('created_at'),
            default  => null,
        };
    }

    // Phân trang
    $products = $products->paginate(12);

    // Dữ liệu phục vụ filter
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
