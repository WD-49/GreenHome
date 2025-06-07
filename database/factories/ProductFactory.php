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
            'category_id' => Category::inRandomOrder()->first()->id,  // Chọn ngẫu nhiên một Category
            'brand_id' => Brand::inRandomOrder()->first()->id,        // Chọn ngẫu nhiên một Brand
                 'name' => $this->faker->words(3, true), 
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'quantity' => $this->faker->numberBetween(10, 100),
            'date_of_entry' => $this->faker->dateTimeThisYear(),
            'status' => $this->faker->boolean(),
            'image' => $this->faker->imageUrl(),
            'view' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
