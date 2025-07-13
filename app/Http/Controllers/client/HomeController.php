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
       
        $banner1 = Banner::where('priority', 1)->get();
        $banner2 = Banner::where('priority', 2)->get();
        $banner3 = Banner::where('priority', 3)->get();
        $banner4 = Banner::where('priority', 4)->get();
        $banner5 = Banner::where('priority', 5)->get();
        $banner6 = Banner::where('priority', 6)->get();
        $banner7 = Banner::where('priority', 7)->get();
        $banner8 = Banner::where('priority', 8)->get();
        $banner9 = Banner::where('priority', 9)->get();
        $banner10 = Banner::where('priority', 10)->get();
        $banner11 = Banner::where('priority', 11)->get();
        $banner12 = Banner::where('priority', 12)->first();


        $products = Product::latest()->take(10)->get(); // Lấy 10 sản phẩm mới nhất
        // Lấy 4 danh mục có số lượng sản phẩm lớn nhất
      $topCategories = Category::withCount('products')
    ->orderBy('products_count', 'desc')
    ->take(4)
    ->get();




        $brands = Brand::all();
        $banners = Banner::where('priority', '>=', 3)->orderBy('priority')->get();
        //Lấy tất cả danh mục, và mỗi danh mục chỉ lấy 8 sản phẩm có view cao nhất
       $categoriesWithTopProducts = Category::with(['products' => function ($query) {
    $query->orderBy('view', 'desc')->take(8);
}])->get();

        $randomProducts = Product::inRandomOrder()->take(8)->get();
        $categories2 = Category::with(['products' => function ($query) {
            $query->latest()->take(8);
        }])->get();
        $banners_mix = Banner::orderBy('priority')->get();
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
        $dealBanner = Banner::where('priority', 13)->where('status', 1)->first();

        $webInfos = DB::table('web_infos')->get()->pluck('value', 'key');
        $categories3 = DB::table('categories')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc') // Mới nhất
            ->limit(5)                      // Giới hạn 5 bản ghi
            ->get();
$categories = Category::all();

        return view('client.pages.home', compact('categories', 'dealBanner', 'categories2', 'products1', 'blogs', 'products', 'banner12', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5', 'banner6', 'banner7', 'banner8', 'banner9', 'banner10', 'topCategories', 'categoriesWithTopProducts', 'categories2', 'randomProducts', 'brands', 'banners', 'banner11', 'banners_mix', 'webInfos', 'categories3'));


    }
}
