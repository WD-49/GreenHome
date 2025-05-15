<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true), // Ví dụ: "Điện tử", "Sách học"
            'description' => $this->faker->sentence(), // Một câu mô tả ngắn
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
