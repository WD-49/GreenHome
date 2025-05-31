<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Tạo 10 sản phẩm giả
        Product::factory(10)->create();
    }
}
