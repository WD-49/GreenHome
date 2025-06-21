<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Lấy các tham số lọc từ request
        $sort = $request->input('sort', 'latest');
        $selectedCategories = (array) $request->input('categories', []);
        $brandId = $request->input('brand_id');

        // Lấy danh sách danh mục và thương hiệu kèm số lượng sản phẩm
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        // Khởi tạo query sản phẩm
        $productsQuery = Product::with([
            'productVariants' => function ($q) {
                $q->whereNotNull('price');
            },
            'brand'
        ])->where('status', 1);

        // Lọc theo danh mục nếu có
        if (!empty(array_filter($selectedCategories))) {
            $productsQuery->whereIn('category_id', $selectedCategories);
        }


        // Lọc theo thương hiệu nếu có
        if (!empty($brandId)) {
            $productsQuery->where('brand_id', $brandId);
        }

        // Sắp xếp theo lựa chọn
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

        // Phân trang kết quả
        $products = $productsQuery->paginate(12)->withQueryString();

        // Trả về view với tất cả dữ liệu cần thiết
        return view('client.pages.shop', compact(
            'products',
            'sort',
            'categories',
            'brands',
            'selectedCategories',
            'brandId'
        ));
    }
}
