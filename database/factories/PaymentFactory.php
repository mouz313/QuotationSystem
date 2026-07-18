<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\ClientUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'quotation_id'   => Quotation::factory(),
            'client_user_id' => \App\Models\ClientUser::factory(),
            'amount'         => fake()->randomFloat(2, 10, 5000),
            'proof'          => null,
            'notes'          => null,
            'status'         => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
