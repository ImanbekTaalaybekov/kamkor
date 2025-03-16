<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'pin' => fake()->unique()->numerify('##############'),
            'phone_number' => fake()->unique()->phoneNumber(),
            'password' => static::$password ??= bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
