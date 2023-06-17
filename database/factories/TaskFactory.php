<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory {

    public function definition()
    {
        return [
            'notes' => $this->faker->text,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
