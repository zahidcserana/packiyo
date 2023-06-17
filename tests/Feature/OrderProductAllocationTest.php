<?php

namespace Tests\Feature;

use App\Http\Requests\Order\StoreBatchRequest;
use App\Http\Requests\Order\UpdateBatchRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Traits\UnitTestSetup;

class OrderProductAllocationTest extends TestCase
{
    use UnitTestSetup, RefreshDatabase, WithFaker;

    public function testCreateAndUpdate(): void
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $data = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($data);

        $orders = app()->order->storeBatch($request);

        foreach ($orders as $key => $order) {
            $this->assertInstanceOf(Order::class, $order);

            $this->assertEquals($data[$key]['number'], $order->number);

            $this->assertTrue($order->customer->users->contains('id', $user->id));

            $orderItems = OrderItem::where('order_id', $order->id)->get();

            foreach ($orderItems as $orderItem) {
                $this->assertEquals($orderItem->product->quantity_on_hand, $orderItem->product->quantity_available + $orderItem->product->quantity_allocated);
            }
        }

        $data = $this->setUpdateData($orders, $customer);

        $request = UpdateBatchRequest::make($data);

        $orders = app()->order->updateBatch($request);

        foreach ($orders as $order) {
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            foreach ($orderItems as $orderItem) {
                $this->assertEquals($orderItem->product->quantity_on_hand, $orderItem->product->quantity_available + $orderItem->product->quantity_allocated);
            }
        }

        $product = Product::with('orderItem')->where('id', $product1->id)->first();
        $this->assertCount(2, $product1->orderItem);

        $orderItemQuantity = 0;
        foreach ($product->orderItem as $orderItem) {
            $orderItemQuantity += $orderItem->quantity;
        }

        $this->assertEquals($orderItemQuantity, $product->quantity_allocated);

        $product = Product::with('orderItem')->where('id', $product2->id)->first();
        $this->assertCount(2, $product2->orderItem);

        $orderItemQuantity = 0;
        foreach ($product->orderItem as $orderItem) {
            $orderItemQuantity += $orderItem->quantity;
        }

        $this->assertEquals($orderItemQuantity, $product->quantity_allocated);
    }

    private function createProduct($customer)
    {
        return factory(Product::class)->create([
            'customer_id' => $customer->id,
            'quantity_on_hand' => 50,
            'quantity_available' => 50,
            'weight' => $this->faker()->numberBetween(0, 100),
            'width' => $this->faker()->numberBetween(0, 100),
            'length' => $this->faker()->numberBetween(0, 100),
            'height' => $this->faker()->numberBetween(0, 100)
        ]);
    }

    /**
     * @param $orders
     * @param $customer
     * @return array
     */
    private function setUpdateData($orders, $customer): array
    {
        $data = [];

        foreach ($orders as $key => $order) {
            $data[$key] = [
                "customer_id" => $customer->id,
                "order_status_id" => $order->order_status_id,
                "number" => $order->number,
                "ordered_at" => date('Y-m-d H:i:s'),
                "required_shipping_date_at" => date('Y-m-d H:i:s'),
                "shipping_date_before_at" => date('Y-m-d H:i:s'),
                "priority" => $this->faker->numberBetween(0, 5),
                'tags' => '',
                "shipping_contact_information" =>
                    [
                        'name' => $this->faker->name,
                        'address' => $this->faker->address,
                        'zip' => $this->faker->postcode,
                        'city' => $this->faker->city,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->phoneNumber,
                        'country_id' => 1
                    ],
                "billing_contact_information" =>
                    [
                        'name' => $this->faker->name,
                        'address' => $this->faker->address,
                        'zip' => $this->faker->postcode,
                        'city' => $this->faker->city,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->phoneNumber,
                        'country_id' => 1
                    ]
            ];

            foreach ($order->orderItems as $item) {
                $data[$key]["order_items"][] = [
                    "order_item_id" => $item->id,
                    "product_id" => $item->product_id,
                    "quantity" => $this->faker->randomNumber(2),
                    "quantity_shipped" => 0
                ];
            }
        }

        return $data;
    }
}
