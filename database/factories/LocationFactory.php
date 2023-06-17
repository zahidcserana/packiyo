<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'pickable' => $this->faker->numberBetween(0, 1),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
