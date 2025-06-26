<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'Home';
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
        $categories1 = Category::all();
        $brands = Brand::all();
       


$banners = Banner::where('priority', '>=', 3)->orderBy('priority')->get();
$categories = Category::all();
$randomProducts = Product::inRandomOrder()->take(8)->get();
$categories2 = Category::with(['products' => function ($query) {
    $query->latest()->take(8);
}])->get();
  $banners_mix = Banner::orderBy('priority')->get();
 $blogs = Blog::where('status', 1)
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

         $reviews = Review::with(['user']) // lấy thông tin người dùng
        ->whereIn('rating', [4, 5])
        ->where('status', 'approved')
        ->latest()
        ->take(10)
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

        return view('client.pages.home', compact('title','dealBanner','categories2','products1','blogs','reviews','products', 'banner12', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5', 'banner6', 'banner7', 'banner8', 'banner9', 'banner10', 'categories1', 'categories', 'categories2','randomProducts', 'brands', 'banners', 'banner11', 'banners_mix'));
    }



}
