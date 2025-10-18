<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'condition_id' => Condition::first()->id ?? 1,
            'name' => $this->faker->words(3, true),
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'image' => 'images/no-image.jpg',
            'is_sold' => false,
        ];
    }
}
