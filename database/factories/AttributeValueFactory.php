<?php

namespace Database\Factories;

use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition(): array
    {
        return [
            'attribute_id' => Attribute::inRandomOrder()->first()->id ?? Attribute::factory(), // random hoặc tạo mới
            'value' => $this->faker->word,
        ];
    }
}
