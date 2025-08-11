<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $variants = ProductVariant::all();

        foreach (range(1, 100) as $i) {
            Review::create([
                'user_id' => $users->random()->id,
                'product_variant_id' => $variants->random()->id,
                'rating' => rand(1, 5),
                'title' => fake()->sentence,
                'content' => fake()->paragraph,
                'status' => fake()->randomElement(['approved', 'pending', 'rejected']),
                'created_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}
