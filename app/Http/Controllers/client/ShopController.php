<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\AttributeValue;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'latest');
        $selectedCategories = (array) $request->input('categories', []);
        $brandId = $request->input('brand_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $selectedAttributeValues = (array) $request->input('attribute_values', []);

        // Load danh mục, thương hiệu, và biến thể để hiển thị ở sidebar
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        $attributeValues = AttributeValue::with('attribute')->get();

        // Query sản phẩm
        $productsQuery = Product::with([
            'productVariants.reviews', // Quan hệ lồng để tính review từ variant
            'brand',
        ])
        ->where('status', 1);

        // Lọc theo danh mục
        if (!empty($selectedCategories)) {
            $productsQuery->whereIn('category_id', $selectedCategories);
        }

        // Lọc theo thương hiệu
        if (!empty($brandId)) {
            $productsQuery->where('brand_id', $brandId);
        }

        // Sắp xếp
        switch ($sort) {
            case 'oldest':
                $productsQuery->orderBy('date_of_entry', 'asc');
                break;
            case 'hot':
                $productsQuery->orderBy('view', 'desc');
                break;
            default:
                $productsQuery->orderBy('date_of_entry', 'desc');
                break;
        }

        // Lọc theo khoảng giá
        if ($minPrice !== null || $maxPrice !== null) {
            $productsQuery->whereHas('productVariants', function ($query) use ($minPrice, $maxPrice) {
                if ($minPrice !== null) {
                    $query->where('price', '>=', $minPrice);
                }
                if ($maxPrice !== null) {
                    $query->where('price', '<=', $maxPrice);
                }
            });
        }

        // Lọc theo giá trị biến thể (attribute_value_id)
        if (!empty($selectedAttributeValues)) {
            $productsQuery->whereHas('productVariants.productVariantValues', function ($q) use ($selectedAttributeValues) {
                $q->whereIn('attribute_value_id', $selectedAttributeValues);
            });
        }

        // Phân trang kết quả
        $products = $productsQuery->paginate(12)->withQueryString();

        return view('client.pages.shop', compact(
            'products',
            'categories',
            'brands',
            'attributeValues',
            'sort',
            'selectedCategories',
            'brandId',
            'minPrice',
            'maxPrice',
            'selectedAttributeValues'
        ));
    }
}
