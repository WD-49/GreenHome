<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shipping_name' => $this->faker->name,
            'shipping_phone' => $this->faker->phoneNumber,
            'shipping_address' => $this->faker->address,
            // 'status_id' => OrderStatus::factory(),
            'discount_id' => $this->faker->randomFloat(2, 0, 100),
            // 'payment_method_id' => PaymentMetho::factory(),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'discount_amount' => $this->faker->randomFloat(2, 0, 100),
            'shippiing_fee' => $this->faker->randomFloat(2, 0, 50),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'note' => $this->faker->sentence,
            'cancel_reason' => $this->faker->sentence,
        ];
    }
}
