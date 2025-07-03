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
            ->get();

        $brands = Brand::where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        $user = Auth::user();

        $view->with([
            'headerCategories' => $categories,
            'headerBrands' => $brands,
            'authUser' => $user,
        ]);
    }
}
