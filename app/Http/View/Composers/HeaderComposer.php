<?php

namespace App\Http\View\Composers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HeaderComposer
{
    public function compose(View $view)
    {
        $categories = Category::where('status', 1)
            ->whereNull('deleted_at')
            ->take(7) // phần dùng cho footer
            ->get();

        $categories3 = Category::where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $brands = Brand::whereNull('deleted_at')->get();

        $user = Auth::user();

        // Danh mục cha kèm sản phẩm (dạng menu dọc)
        $menuCategories = Category::with(['products' => function ($q) {
            $q->where('status', 1)->take(5);
        }])
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $view->with([
            'headerCategories' => $categories,
            'headerBrands' => $brands,
            'authUser' => $user,
            'categories3' => $categories3, // ✅ Truyền thêm biến này
            'menuCategories'   => $menuCategories, // ✅ Thêm menu có sản phẩm
        ]);
    }
}
