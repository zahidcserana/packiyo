<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory {

    public function definition()
    {
        $customers = Customer::all();
        $orderStatuses = OrderStatus::all();

        return [
            'number' => $this->faker->unique()->randomNumber(7),
            "priority" => $this->faker->numberBetween(0, 10),
            'created_at' => now(),
            'updated_at' => now(),
            'order_status_id' => $orderStatuses->random()->id,
            'customer_id' => $customers->count() > 10 ? $customers->random()->id : Customer::factory()->create()
        ];
    }
}
