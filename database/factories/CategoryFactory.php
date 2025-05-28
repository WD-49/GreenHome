<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),  // Đảm bảo tên là duy nhất
            'slug' => function (array $attributes) {
                // Tạo slug từ tên và thêm số ngẫu nhiên để tránh trùng lặp
                return Str::slug($attributes['name']) . '-' . $this->faker->unique()->numberBetween(1, 1000);  
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
