<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id'   => Category::inRandomOrder()->first()?->id ?? 1,
            'brand_id'      => Brand::inRandomOrder()->first()?->id ?? 1,
            'name'          => $this->faker->words(3, true),
            'slug'          => $this->faker->unique()->slug(),
            'description'   => $this->faker->paragraph,
            'quantity'      => $this->faker->numberBetween(5, 200),
            'date_of_entry' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status'        => $this->faker->randomElement([0, 1]),
            'image'         => 'products/' . $this->faker->image('public/storage/products', 400, 400, null, false), // đường dẫn ảnh fake
            'view'          => $this->faker->numberBetween(0, 999),
        ];
    }
}
