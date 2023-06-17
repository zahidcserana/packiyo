<?php

namespace Database\Factories;

use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'secret_key' => $this->faker->word,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
