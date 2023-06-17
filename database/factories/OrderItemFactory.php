<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory {

    public function definition()
    {
        $products = Product::all();

        return [
            'order_id' => Order::all()->sortByDesc('created_at')->first()->id,
            'product_id' => $products->count() > 20 ? $products->random()->id : Product::factory()->create(),
            'quantity' => $this->faker->randomNumber(2),
            'quantity_shipped' => $this->faker->randomNumber(2),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
