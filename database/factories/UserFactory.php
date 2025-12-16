<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {

        $name = fake()->firstName() . ' ' . fake()->lastName();
        $emailName = Str::slug($name, ''); 
        $email = $emailName . rand(1, 999) . '@kost.com';

        return [
            'name'              => $name,
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('Password_123'),
            'role'              => 'User',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function isAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'remember_token'    => Str::random(10),
            'role' => 'Admin',
        ]);
    }

}
