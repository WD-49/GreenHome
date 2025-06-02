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
        $category_id = fake()->numberBetween(1, 10); // Bạn sửa lại số phù hợp với dữ liệu Category
    $brand_id = fake()->numberBetween(1, 10);    // Sửa theo số Brand có trong DB
    $name = fake()->unique()->words(3, true);

    return [
        'category_id' => $category_id,
        'brand_id' => $brand_id,
        'name' => ucfirst($name),
        'slug' => Str::slug($name . '-' . Str::random(5)),
        'description' => fake()->optional()->text(200),
        'quantity' => fake()->numberBetween(1, 1000),
        'date_of_entry' => fake()->optional()->dateTimeBetween('-1 years', 'now'),
        'status' => fake()->boolean(),
        'image' => fake()->optional()->imageUrl(640, 480, 'products', true),
        'view' => fake()->numberBetween(0, 5000),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    }
}
