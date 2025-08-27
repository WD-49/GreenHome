<?php

namespace App\Http\Controllers\client;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {

        $banners = Banner::where('status', 1)
            ->get();
        $products = Product::latest()->take(10)->get(); // Lấy 10 sản phẩm mới nhất
        // Lấy 4 danh mục có số lượng sản phẩm lớn nhất
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(4)
            ->get();

        $brands = Brand::all();

        //Lấy tất cả danh mục, và mỗi danh mục chỉ lấy 8 sản phẩm có view cao nhất
        $categoriesWithTopProducts = Category::with(['products' => function ($query) {
            $query->orderBy('view', 'desc')->take(8);
        }])->take(10)->get();

        $popularProducts = Product::orderByDesc('view')->take(8)->get();
        $categories2 = Category::with(['products' => function ($query) {
            $query->latest()->take(8);
        }])->get();
        $blogs = Blog::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $products1 = Product::with('productVariants')
            ->has('productVariants') // chỉ lấy sản phẩm có ít nhất 1 biến thể
            ->get();
        $categories2 = Category::with([
            'products' => function ($query) {
                $query->with('productVariants');
            }
        ])->get();

        $categories3 = DB::table('categories')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc') // Mới nhất
            ->limit(5)                      // Giới hạn 5 bản ghi
            ->get();
        $categories = Category::all();

        return view('client.pages.home', compact('categories', 'categories2', 'products1', 'blogs', 'products', 'topCategories', 'categoriesWithTopProducts', 'categories2', 'popularProducts', 'brands', 'categories3', 'banners'));
    }
}
