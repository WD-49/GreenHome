<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run()
    {
        // Tạo 10 thương hiệu giả
        Brand::factory(10)->create();
    }
}
