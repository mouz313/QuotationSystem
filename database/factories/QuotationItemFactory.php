<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 10);
        $price = fake()->randomFloat(2, 50, 10000);
        return [
            'quotation_id'     => Quotation::factory(),
            'item_title'       => fake()->words(3, true),
            'item_description' => fake()->sentence(),
            'quantity'         => $qty,
            'unit_price'       => $price,
            'subtotal'         => round($qty * $price, 2),
            'sort_order'       => 0,
        ];
    }
}
