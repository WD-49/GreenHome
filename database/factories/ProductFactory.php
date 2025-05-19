<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? Brand::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => $price = fake()->randomFloat(2, 50, 1000),
            'promotional_price' => fn() => fake()->boolean(70) ? fake()->randomFloat(2, 30, $price) : null,
            'quantity' => fake()->numberBetween(1, 100),
            'date_of_entry' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->boolean(),
            'image' => fake()->imageUrl(640, 480, 'products', true),
            'view' => fake()->numberBetween(0, 5000),
        ];
    }
}
