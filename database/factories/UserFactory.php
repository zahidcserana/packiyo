<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('secret'), // secret
            'remember_token' => Str::random(10),
            'user_role_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
