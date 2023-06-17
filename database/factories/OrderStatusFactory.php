<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
