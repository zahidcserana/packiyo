<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Order\DestroyBatchRequest;
use App\Http\Requests\Order\StoreBatchRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateBatchRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ShipmentResource;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LocationProduct;
use App\Models\Shipment;
use Tests\Unit\Traits\UnitTestSetup;
use DB;

class OrderTest extends TestCase
{
    use RefreshDatabase, UnitTestSetup, WithFaker;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $adminUser = $this->createAdministrator();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $data = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = StoreRequest::make($data);

        $order = app()->order->store($request);

        $orderResource = (new OrderResource($order))->resolve();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.order.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [],
                'links' => [],
                'meta' => []
            ]
        );

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($orderResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $data = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($data);

        $orders = app()->order->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($orders as $key => $order) {
            $this->assertInstanceOf(Order::class, $order);

            $this->assertEquals($data[$key]['number'], $order->number);

            foreach ($order->orderItems as $item) {
                $this->assertInstanceOf(OrderItem::class, $item);

                $this->assertEquals($item->order_id, $order->id);
            }

            $this->assertTrue($order->customer->users->contains('id', $user->id));

            $this->assertFalse($order->customer->users->contains('id', $guestUser->id));
        }

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($user, 'api')->json('POST', route('api.order.store'), $data)->assertStatus(200);

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.order.store'), $data)->assertStatus(403);
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForStore = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $orders = app()->order->storeBatch($request);

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
                    "quantity" => $this->faker->randomNumber(1),
                    "quantity_shipped" => 0
                ];
            }
        }

        $request = UpdateBatchRequest::make($data);

        $orders = app()->order->updateBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($orders as $key => $order) {
            $this->assertInstanceOf(Order::class, $order);

            $this->assertEquals($data[$key]['number'], $order->number);

            $this->assertEquals($data[$key]['ordered_at'], $order->ordered_at);

            foreach ($order->orderItems as $item) {
                $this->assertInstanceOf(OrderItem::class, $item);

                $this->assertEquals($item->order_id, $order->id);
            }

            $this->assertTrue($order->customer->users->contains('id', $user->id));

            $this->assertFalse($order->customer->users->contains('id', $guestUser->id));

            $this->assertTrue($user->can('update', $order));

            $this->assertFalse($guestUser->can('update', $order));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataStore = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($dataStore);

        $orders = app()->order->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $data = [];

        foreach ($orders as $key => $order) {
            $this->assertTrue($user->can('delete', $order));

            $this->assertFalse($guestUser->can('delete', $order));

            $data[$key] = [
                "id" => $order->id,
            ];
        }

        $request = DestroyBatchRequest::make($data);

        $ids = app()->order->destroyBatch($request);

        foreach ($ids as $key => $value) {
            $this->assertSoftDeleted('orders', ['id' => $value['id']]);
        }
    }

    public function testShip()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $data = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = StoreRequest::make($data);

        $order = app()->order->store($request);

        $shipment = Shipment::create([
            'order_id' => $order->id
        ]);

        $this->createContactInformation($shipment);

        $shipmentResource = (new ShipmentResource($shipment))->resolve();

        $warehouse = $this->createWarehouse($customer);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $this->createLocationProduct($location1, $product1);

        $this->createLocationProduct($location2, $product2);

        $orderItems = $order->orderItems;

        if (count($orderItems) < 1) {
            $orderItems = OrderItem::where('order_id', $order->id)->get();
        }

        $data = [
            "order_items" => [
                [
                    'order_item_id' => $orderItems[0]->id,
                    'location_id' => $location1->id,
                    'quantity' => $this->faker->numberBetween(1, 9),
                ],
                [
                    'order_item_id' => $orderItems[1]->id,
                    'location_id' => $location2->id,
                    'quantity' => $this->faker->numberBetween(1, 9),
                ]
            ],
            "contact_information" => [
                    [
                        'name' => $this->faker->name,
                        'address' => $this->faker->address,
                        'zip' => $this->faker->postcode,
                        'city' => $this->faker->city,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->phoneNumber,
                        'country_id' => 1
                ]
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('POST', route('api.order.ship', $order->id), $data);

        $response->assertStatus(200);

        $this->assertEmpty(array_diff_key($shipmentResource, $response->json()));

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.order.ship', $order->id), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.order.ship', $order->id), $data)->assertStatus(403);
    }

    public function testShipQuantityCalculation()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $data = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = StoreRequest::make($data);

        $order = app()->order->store($request);

        $shipment = Shipment::create([
            'order_id' => $order->id
        ]);

        $this->createContactInformation($shipment);

        $warehouse = $this->createWarehouse($customer);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $productLocation1 = $this->createLocationProduct($location1, $product1);

        $productLocation2 = $this->createLocationProduct($location2, $product2);

        $orderItems = $order->orderItems;

        if (count($orderItems) < 1) {
            $orderItems = OrderItem::where('order_id', $order->id)->get();
        }

        $data = [
            "order_items" => [
                [
                    'order_item_id' => $orderItems[0]->id,
                    'location_id' => $location1->id,
                    'quantity' => $this->faker->numberBetween(1, 9),
                ],
                [
                    'order_item_id' => $orderItems[1]->id,
                    'location_id' => $location2->id,
                    'quantity' => $this->faker->numberBetween(1, 9),
                ]
            ],
            "contact_information" => [
                    [
                        'name' => $this->faker->name,
                        'address' => $this->faker->address,
                        'zip' => $this->faker->postcode,
                        'city' => $this->faker->city,
                        'email' => $this->faker->unique()->safeEmail,
                        'phone' => $this->faker->phoneNumber,
                        'country_id' => 1
                ]
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('POST', route('api.order.ship', $order->id), $data);

        $updatedProductLocation1 = LocationProduct::where('location_id', $location1->id)->where('product_id', $product1->id)->first();

        $updatedProductLocation2 = LocationProduct::where('location_id', $location2->id)->where('product_id', $product2->id)->first();

        $this->assertEquals(($productLocation1->quantity_on_hand - $data['order_items'][0]['quantity']), $updatedProductLocation1->quantity_on_hand);

        $this->assertEquals(($productLocation2->quantity_on_hand - $data['order_items'][1]['quantity']), $updatedProductLocation2->quantity_on_hand);

        $updatedProduct1 = Product::find($product1->id);

        $updatedProduct2 = Product::find($product2->id);

        $this->assertEquals(($product1->quantity_on_hand - $data['order_items'][0]['quantity']), $updatedProduct1->quantity_on_hand);

        $this->assertEquals(($product2->quantity_on_hand - $data['order_items'][1]['quantity']), $updatedProduct2->quantity_on_hand);
    }

    public function testHistory()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForStore = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $orders = app()->order->storeBatch($request);

        foreach ($orders as $order) {
            $order->ordered_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $order->required_shipping_date_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $order->shipping_date_before_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $order->priority = $this->faker->numberBetween(0, 5);

            $order->save();


            $response = $this->actingAs($user, 'api')->json('GET', route('api.order.history', $order->id));

            foreach ($response->json()['data'] as $res) {
                $this->assertEquals($res['revisionable_type'], Order::class);
                $this->assertEquals($res['revisionable_id'], $order->id);

                $key = $res['key'];

                $order = Order::find($res['revisionable_id']);

                $this->assertNotEquals($res['old_value'], $order->$key);
                $this->assertEquals($res['new_value'], $order->$key);
            }
        }
    }

    public function testFilter()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $adminUser = $this->createAdministrator();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForStore = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $orders = app()->order->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $item->quantity = $this->faker->numberBetween(1, 9);
                $item->save();

                $response = $this->actingAs($user, 'api')->json('GET', route('api.order.itemHistory', $item->id));

                foreach ($response->json()['data'] as $res) {
                    $this->assertEquals($res['revisionable_type'], OrderItem::class);
                    $this->assertEquals($res['revisionable_id'], $item->id);

                    $key = $res['key'];

                    $item = OrderItem::find($res['revisionable_id']);

                    $this->assertNotEquals($res['old_value'], $item->$key);
                    $this->assertEquals($res['new_value'], $item->$key);
                }
            }
        }

        $data = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = StoreRequest::make($data);

        $order = app()->order->store($request);

        $orderResource = (new OrderResource($order))->resolve();

        $data = [
            'from_date_created' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_created' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'from_date_updated' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_updated' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d')
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.order.filter', $data));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($orderResource, $res));
        }
    }
}
