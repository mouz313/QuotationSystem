<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    private static array $usedCodes = [];

    public function definition(): array
    {
        do {
            $code = strtoupper(fake()->bothify('??'));
        } while (in_array($code, self::$usedCodes));
        self::$usedCodes[] = $code;

        return [
            'code'       => $code,
            'name'       => fake()->word(),
            'symbol'     => '$',
            'is_default' => false,
            'is_active'  => true,
        ];
    }

    public static function resetUniqueState(): void
    {
        self::$usedCodes = [];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
