<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyPackageFactory extends Factory
{
    protected $model = CompanyPackage::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'package_id' => Package::factory(),
            'start_date' => now()->toDateString(),
            'end_date'   => now()->addDays(30)->toDateString(),
            'status'     => 'active',
        ];
    }
}
