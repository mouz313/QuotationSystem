<?php

namespace Database\Factories;

use App\Models\ClientUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientUserFactory extends Factory
{
    protected $model = ClientUser::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'password'   => static::$password ??= Hash::make('password'),
            'phone'      => fake()->phoneNumber(),
            'is_active'  => true,
        ];
    }
}
