<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'name'           => fake()->word() . ' Plan',
            'description'    => fake()->sentence(),
            'price'          => fake()->randomFloat(2, 0, 500),
            'currency_code'  => 'USD',
            'duration_days'  => 30,
            'max_users'      => 5,
            'max_clients'    => 50,
            'max_quotations' => 100,
            'is_active'      => true,
            'sort_order'     => 0,
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'name' => 'Free',
            'price' => 0,
            'max_users' => 1,
            'max_clients' => 5,
            'max_quotations' => 10,
        ]);
    }

    public function professional(): static
    {
        return $this->state(fn () => [
            'name' => 'Professional',
            'price' => 49.99,
            'max_users' => 10,
            'max_clients' => 100,
            'max_quotations' => 500,
        ]);
    }
}
