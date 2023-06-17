<?php

namespace Database\Factories;

use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTypeFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
