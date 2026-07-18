<?php

namespace Database\Factories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->word() . ' Tax',
            'percentage' => fake()->randomFloat(2, 1, 25),
            'is_default' => false,
            'is_active'  => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
