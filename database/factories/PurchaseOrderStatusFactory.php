<?php

namespace Database\Factories;

use App\Models\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderStatusFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
