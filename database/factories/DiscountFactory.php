<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->sentence(),
         'code' => fake()->unique()->word(),

            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->numberBetween(1, 100),
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'max_discount' => fake()->numberBetween(1, 1000),
            'min_order_value' => fake()->numberBetween(1, 100),
            'quantity' => fake()->numberBetween(1, 100),
            'user_usage_limit' => fake()->numberBetween(1, 10),
            'applies_to_all_products' => fake()->boolean(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'created_by' => 1,

        ];
    }
}
