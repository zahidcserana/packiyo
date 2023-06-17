<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnStatusFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
