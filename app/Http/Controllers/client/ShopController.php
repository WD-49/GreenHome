<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use App\Models\WishList;

use Illuminate\Support\Facades\Auth;



class ShopController extends Controller
{
    public function index(Request $request)
    {
        $productsQuery = Product::query()
            ->with(['brand', 'productVariants.productVariantValues', 'productVariants.reviews'])
            ->where('status', 1);

        // Lọc danh mục
        $selectedCategories = array_filter($request->input('categories', []));
        if (!empty($selectedCategories)) {
            $productsQuery->whereIn('category_id', $selectedCategories);
        }


        // Lọc thương hiệu
        $selectedBrandId = $request->input('brand_id');
        if (!empty($selectedBrandId)) {
            $productsQuery->where('brand_id', $selectedBrandId);
        }

        // Lọc theo số sao đánh giá
        if ($request->filled('rating')) {
            $star = intval($request->input('rating'));
            $productsQuery->whereHas('productVariants', function ($q) use ($star) {
                $q->whereHas('reviews', function ($q2) use ($star) {
                    $q2->select('product_variant_id')
                        ->groupBy('product_variant_id')
                        ->havingRaw('AVG(rating) >= ?', [$star])
                        ->havingRaw('AVG(rating) < ?', [$star + 1]);
                });
            });
        }

        // Lọc theo biến thể
        if ($request->filled('attribute_values')) {
            $attributeValueIds = $request->input('attribute_values');
            foreach ($attributeValueIds as $valueId) {
                $productsQuery->whereHas('productVariants.productVariantValues', function ($q) use ($valueId) {
                    $q->where('attribute_value_id', $valueId);
                });
            }
        }

        // Lọc theo khoảng giá
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        if ($min !== null || $max !== null) {
            $min = is_numeric($min) ? floatval($min) : 0;
            $max = is_numeric($max) && floatval($max) > 0 ? floatval($max) : 1000000000;

            if ($min > $max) {
                [$min, $max] = [$max, $min];
            }

            $productsQuery->whereHas('productVariants', function ($q) use ($min, $max) {
                $q->whereBetween('price', [$min, $max]);
            });
        }

        // Sắp xếp
        switch ($request->input('sort')) {
            case 'oldest':
                $productsQuery->oldest();
                break;
            case 'hot':
                $productsQuery->orderByDesc('view');
                break;
            default:
                $productsQuery->latest();
        }

        // Phân trang kết quả
        $products = $productsQuery->paginate(12);

        // Danh sách sản phẩm đã có trong wishlist của user
        $wishlistProductIds = [];
        if (Auth::check()) {
            $wishlistProductIds = \App\Models\WishList::where('user_id', Auth::id())->pluck('product_id')->toArray();

        }


        // Dữ liệu filter
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        $attributeValues = AttributeValue::with('attribute')->get();

        return view('client.pages.shop', compact(
            'products',
            'categories',
            'brands',
            'attributeValues',
            'wishlistProductIds'
        ));
    }
}
