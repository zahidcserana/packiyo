<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulkShipTestSeeder extends Seeder
{
    protected const SEED_NUMBER = 100000;

    public function run()
    {
        $customerId = Customer::first()->id;

        $this->createSampleProducts($customerId);

        $orderStatusId = OrderStatus::factory()->create(['customer_id' => $customerId])->id;

        $this->createOrders($orderStatusId, $customerId);

        $this->addOrderItems();
    }

    private function createSampleProducts($customerId): void
    {
        $products = [];

        for ($i = 0; $i < self::SEED_NUMBER; $i++) {
            $products[] = [
                'customer_id' => $customerId,
                'sku' => random_int(10000, 99999),
                'name' => 'Product ' . $i,
                'price' => 10 * $i,
                'notes' => 'Notes ' . $i,
                'weight' => random_int(1,10),
                'height' => random_int(1,10),
                'width' => random_int(1,10),
                'length' => random_int(1,10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $products = collect($products);

        foreach ($products->chunk(5000) as $productsChunk) {
            DB::table('products')->insert($productsChunk->toArray());
        }
    }

    private function createOrders($orderStatusId, $customerId): void
    {
        $orders = [];

        for ($i = 0; $i < self::SEED_NUMBER; $i++) {
            $orders[] = [
                'customer_id' => $customerId,
                'order_status_id' => $orderStatusId,
                'number' => random_int(1000000, 9999999),
                'priority' => random_int(0, 10),
                'shipping_contact_information_id' => random_int(10, 20),
                'billing_contact_information_id' => random_int(10, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $orders = collect($orders);

        foreach ($orders->chunk(5000) as $ordersChunk) {
            DB::table('orders')->insert($ordersChunk->toArray());
        }
    }

    private function addOrderItems(): void
    {
        $orders = Order::lazy();

        foreach ($orders as $order) {
            dispatch(function () use ($order) {
                app()->order->updateOrderItems($order, [
                    [
                        'product_id' => random_int(1, 1000),
                        'quantity' => random_int(1, 100),
                        'quantity_shipped' => 0,
                    ],
                    [
                        'product_id' => random_int(1, 1000),
                        'quantity' => random_int(1, 100),
                        'quantity_shipped' => 0,
                    ],
                    [
                        'product_id' => random_int(1, 1000),
                        'quantity' => random_int(1, 100),
                        'quantity_shipped' => 0,
                    ],
                ]);
            });
        }
    }
}