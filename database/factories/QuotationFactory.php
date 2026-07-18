<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Currency;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'client_id'    => Client::factory(),
            'currency_id'  => Currency::factory(),
            'quote_number' => 'QT-' . now()->format('Ymd') . '-' . strtoupper(fake()->bothify('####')),
            'type'         => 'simple',
            'issue_date'   => now()->toDateString(),
            'expiry_date'  => now()->addDays(30)->toDateString(),
            'grand_total'  => fake()->randomFloat(2, 100, 50000),
            'status'       => 'draft',
            'payment_status' => 'unpaid',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function sent(): static
    {
        return $this->state(fn () => ['status' => 'sent']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }

    public function milestone(): static
    {
        return $this->state(fn () => ['type' => 'milestone']);
    }
}
