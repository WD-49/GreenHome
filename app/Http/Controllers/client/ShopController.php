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

    $productsQuery->whereIn('id', function ($sub) use ($star) {
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







        // Lọc theo biến thể
        if ($request->filled('attribute_values')) {
            $attributeValueIds = collect($request->input('attribute_values', []))->map(fn($id) => (int) $id);

            // Lấy danh sách Attribute ID và group lại theo thuộc tính
            $attributeValues = \App\Models\AttributeValue::with('attribute')->whereIn('id', $attributeValueIds)->get();

            $groupedByAttr = $attributeValues->groupBy(fn($val) => $val->attribute->id);

            foreach ($groupedByAttr as $attributeId => $values) {
                $valueIds = $values->pluck('id')->toArray();

                $productsQuery->whereHas('productVariants.productVariantValues', function ($q) use ($valueIds) {
                    $q->whereIn('attribute_value_id', $valueIds);
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
