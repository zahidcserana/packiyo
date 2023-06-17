<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory {

    public function definition()
    {
        return [
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
