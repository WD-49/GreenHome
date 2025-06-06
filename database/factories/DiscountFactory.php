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
   public function definition()
{
    return [
        'title' => $this->faker->sentence(3),  // Tạo 1 tiêu đề giả
        'description' => $this->faker->paragraph(),
        'code' => $this->faker->unique()->bothify('CODE-####'),
        'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
        'discount_value' => $this->faker->randomFloat(2, 1, 100),
        'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        'end_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        'max_discount' => $this->faker->randomFloat(2, 0, 100),
        'min_order_value' => $this->faker->randomFloat(2, 10, 1000),
        'quantity' => $this->faker->numberBetween(1, 1000),
        'user_usage_limit' => $this->faker->numberBetween(1, 10),
        'applies_to_all_products' => $this->faker->boolean(),
        'status' => $this->faker->randomElement(['active', 'inactive', 'expired']),
        'created_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

}
