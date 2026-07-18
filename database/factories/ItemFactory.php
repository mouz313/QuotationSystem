<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit_price'  => fake()->randomFloat(2, 10, 10000),
        ];
    }
}
