<?php

namespace App\Http\View\Composers;

use App\Models\WebInfo;
use App\Models\Category;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view)
    {
        // Lấy categories hiển thị ở footer
        $categories = Category::where('status', 1)
            ->whereNull('deleted_at')
            ->take(7)
            ->get();

        // Lấy WebInfo dạng key => value để dễ truy xuất ở view
        $web_info = WebInfo::pluck('value', 'key')->toArray();

        $view->with([
            'footerCategories' => $categories,
            'footerWebInfo' => $web_info,
        ]);
    }
}
