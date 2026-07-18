<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->company(),
            'email'    => fake()->unique()->safeEmail(),
            'phone'    => fake()->phoneNumber(),
            'address'  => fake()->address(),
            'status'   => 'active',
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['status' => 'blocked']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
