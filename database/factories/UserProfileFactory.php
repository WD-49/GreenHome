<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // tạo user tương ứng (hoặc thay bằng ID có sẵn)
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'gender' => $this->faker->randomElement(['male', 'female', 'others']),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'), // ít nhất 18 tuổi
            'user_image' => $this->faker->imageUrl(200, 200, 'people', true, 'User'), // ảnh giả
        ];
    }
}
