<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Category;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả danh mục có sẵn
        $categories = Category::all();

        foreach ($categories as $category) {
            // Tạo 2 banner có priority từ 3 cho mỗi danh mục
            for ($i = 0; $i < 2; $i++) {
                Banner::create([
                    'category_id' => $category->id,
                    'name' => 'Banner ' . ($i + 1) . ' for ' . $category->name,
                    'img' => 'images/banners/sample_' . rand(1, 5) . '.jpg', // Đường dẫn giả định
                    'description' => 'This is banner ' . ($i + 1) . ' for ' . $category->name,
                    'link' => 'https://example.com/' . $category->slug,
                    'priority' => 3 + $i, // Ưu tiên bắt đầu từ 3
                    'status' => 1,
                ]);
            }
        }
    }
}
