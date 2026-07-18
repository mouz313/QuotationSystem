<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Package;
use App\Models\PackageOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageOrderFactory extends Factory
{
    protected $model = PackageOrder::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'package_id'     => Package::factory(),
            'amount'         => fake()->randomFloat(2, 10, 500),
            'currency_code'  => 'USD',
            'status'         => 'pending',
            'payment_method' => 'bank_transfer',
        ];
    }
}
