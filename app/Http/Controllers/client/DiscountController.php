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
    
    // Lấy voucher
    $voucher = Discount::where('code', $code)->firstOrFail();

    // Query sản phẩm áp dụng mã giảm giá
    $products = Product::query()
        ->when($voucher->applies_to_all_products, function ($query) {
            $query->whereNull('deleted_at');
        }, function ($query) use ($code) {
            $query->whereHas('discounts', function ($q) use ($code) {
                $q->where('code', $code);
            });
        })
        ->with(['brand', 'productVariants', 'reviews'])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating');


     
             // Tìm kiếm theo tên sản phẩm hoặc mô tả
if ($request->filled('search')) {
    $searchTerm = $request->input('search');
    $products->where(function ($query) use ($searchTerm) {
        $query->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
    });
}






    // ✅ Lọc danh mục
    $selectedCategories = array_filter($request->input('categories', []));
        if (!empty($selectedCategories)) {
            $products->whereIn('category_id', $selectedCategories);
        }
     // Lọc thương hiệu
        $selectedBrandId = $request->input('brand_id');
        if (!empty($selectedBrandId)) {
            $products->where('brand_id', $selectedBrandId);
        }


    // ✅ Lọc theo số sao đánh giá
     // Lọc theo số sao đánh giá
        if ($request->filled('rating')) {
    $star = intval($request->input('rating'));

    $products->whereIn('id', function ($sub) use ($star) {
        $sub->select('products.id')
            ->from('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('reviews', 'product_variants.id', '=', 'reviews.product_variant_id')
            ->whereNull('reviews.deleted_at')
            ->groupBy('products.id')
            ->havingRaw('AVG(reviews.rating) >= ?', [$star])
            ->havingRaw('AVG(reviews.rating) < ?', [$star + 1]);
    }); 

    
}


    // ✅ Lọc theo biến thể (attribute_values)
    if ($request->filled('attribute_values')) {
        $attributeValueIds = collect($request->input('attribute_values', []))->map(fn($id) => (int) $id);

        // Lấy danh sách Attribute ID và group lại theo thuộc tính
        $attributeValues = AttributeValue::with('attribute')->whereIn('id', $attributeValueIds)->get();
        $groupedByAttr = $attributeValues->groupBy(fn($val) => $val->attribute->id);

        foreach ($groupedByAttr as $attributeId => $values) {
            $valueIds = $values->pluck('id')->toArray();

            $products->whereHas('productVariants.productVariantValues', function ($q) use ($valueIds) {
                $q->whereIn('attribute_value_id', $valueIds);
            });
        }
    }

    // ✅ Lọc theo khoảng giá
    $min = $request->input('min_price');
    $max = $request->input('max_price');
    if ($min !== null || $max !== null) {
        $min = is_numeric($min) ? floatval($min) : 0;
        $max = is_numeric($max) && floatval($max) > 0 ? floatval($max) : 1000000000;

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $products->whereHas('productVariants', function ($q) use ($min, $max) {
            $q->whereBetween('price', [$min, $max]);
        });
    }

    // ✅ Sắp xếp
    if ($request->filled('sort')) {
        match ($request->sort) {
            'latest' => $products->latest(),
            'oldest' => $products->oldest(),
            'hot' => $products->orderByDesc('created_at'), // Hoặc theo reviews_avg_rating
            default => null,
        };
    }

    // ✅ Phân trang
    $products = $products->paginate(12);

    // ✅ Dữ liệu filter
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
