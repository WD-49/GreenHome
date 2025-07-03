<?php

namespace App\Http\View\Composers;

use App\Models\Brand;
use App\Models\WebInfo;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HeaderComposer
{
    public function compose(View $view)
    {
        $categories = Category::where('status', 1)
            ->whereNull('deleted_at')
            ->take(7)
            ->get();

        $web_info = WebInfo::get();
        $view->with([
            'footerCategories' => $categories,
            'footerWebInfo' => $web_info,
        ]);
    }
}
