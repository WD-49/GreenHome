<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'category_id' => Category::factory(), // Tạo Category tự động liên kết
            'brand_id' => Brand::factory(), // Tạo Brand tự động liên kết
            'name' => $this->faker->word(),
            'slug' => function (array $attributes) {
                return Str::slug($attributes['name']); // Tạo slug tự động từ tên
            },
            'description' => $this->faker->paragraph(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'date_of_entry' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->boolean(),
            'image' => $this->faker->imageUrl(640, 480, 'products'),
            'view' => $this->faker->numberBetween(0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
