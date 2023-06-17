<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Return_\DestroyBatchRequest;
use App\Http\Requests\Return_\StoreBatchRequest;
use App\Http\Requests\Return_\StoreRequest;
use App\Http\Requests\Return_\UpdateBatchRequest;
use App\Http\Requests\Order\StoreRequest as OrderStoreRequest;
use App\Http\Resources\ReturnResource;
use App\Http\Resources\ReturnItemResource;
use App\Models\Product;
use App\Models\LocationProduct;
use App\Models\Order;
use App\Models\Return_;
use App\Models\ReturnItem;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class ReturnTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $adminUser = $this->createAdministrator();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $data = $this->getReturnRequestData($customer, $product1, $product2, $order);

        $request = StoreRequest::make($data);

        $return = app()->return->store($request);

        $returnResource = (new ReturnResource($return))->resolve();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.return.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [],
                'links' => [],
                'meta' => []
            ]
        );

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($returnResource, $res));
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

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $data = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $request = StoreBatchRequest::make($data);

        $returns = app()->return->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($returns as $key => $return) {
            $this->assertInstanceOf(Return_::class, $return);

            $this->assertInstanceOf(Order::class, $return->order);

            $this->assertEquals($data[$key]['number'], $return->number);

            foreach ($return->returnItems as $item) {
                $this->assertInstanceOf(ReturnItem::class, $item);

                $this->assertEquals($item->return_id, $return->id);
            }

            $this->assertTrue($return->order->customer->users->contains('id', $user->id));

            $this->assertFalse($return->order->customer->users->contains('id', $guestUser->id));
        }

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($user, 'api')->json('POST', route('api.return.store'), $data)->assertStatus(200);

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.return.store'), $data)->assertStatus(403);
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $dataForStore = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $returns = app()->return->storeBatch($request);

        $data = [];

        foreach ($returns as $key => $return) {
            $data[$key] = [
                "number" => $return->number,
                "requested_at" => date('Y-m-d H:i:s'),
                "expected_at" => date('Y-m-d H:i:s'),
                "received_at" => date('Y-m-d H:i:s'),
                "approved" => $this->faker->numberBetween(0, 1),
                "notes" => $this->faker->text,
                "reason" => $this->faker->text,
            ];

            foreach ($return->returnItems as $item) {
                $data[$key]["return_items"][] = [
                    "order_item_id" => $item->id,
                    "product_id" => $item->product_id,
                    "quantity" => $this->faker->randomNumber(1),
                    "quantity_received" => 10
                ];
            }
        }

        $request = UpdateBatchRequest::make($data);

        $returns = app()->return->updateBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($returns as $key => $return) {
            $this->assertInstanceOf(Return_::class, $return);

            $this->assertInstanceOf(Order::class, $order);

            $this->assertEquals($data[$key]['number'], $return->number);

            $this->assertTrue($return->order->customer->users->contains('id', $user->id));

            $this->assertFalse($return->order->customer->users->contains('id', $guestUser->id));

            $this->assertTrue($user->can('update', $return));

            $this->assertFalse($guestUser->can('update', $return));
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

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $data = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $request = StoreBatchRequest::make($data);

        $returns = app()->return->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $data = [];

        foreach ($returns as $key => $return) {
            $this->assertTrue($user->can('delete', $return));

            $this->assertFalse($guestUser->can('delete', $return));

            $data[$key] = [
                "id" => $return->id,
            ];
        }

        $request = DestroyBatchRequest::make($data);

        $ids = app()->return->destroyBatch($request);

        foreach ($ids as $key => $value) {
            $this->assertSoftDeleted('returns', ['id' => $value['id']]);
        }
    }

    public function testReceive()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $data = $this->getReturnRequestData($customer, $product1, $product2, $order);

        $request = StoreRequest::make($data);

        $return = app()->return->store($request);

        $returnItemResource = (new ReturnItemResource($return->returnItems[0]))->resolve();

        $warehouse = $this->createWarehouse($customer);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $this->createLocationProduct($location1, $product1);

        $this->createLocationProduct($location2, $product2);

        $data = [
            [
                'return_item_id' => $return->returnItems[0]->id,
                'location_id' => $location1->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ],
            [
                'return_item_id' => $return->returnItems[1]->id,
                'location_id' => $location2->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('POST', route('api.return.receive', $return->id), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($returnItemResource, $res));
        }

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.return.receive', $return->id), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.return.receive', $return->id), $data)->assertStatus(403);
    }

    public function testReceiveQuantityCalculation()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $orderStatus = $this->createOrderStatus($customer);

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $data = $this->getReturnRequestData($customer, $product1, $product2, $order);

        $request = StoreRequest::make($data);

        $return = app()->return->store($request);

        $warehouse = $this->createWarehouse($customer);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $productLocation1 = $this->createLocationProduct($location1, $product1);

        $productLocation2 = $this->createLocationProduct($location2, $product2);

        $data = [
            [
                'return_item_id' => $return->returnItems[0]->id,
                'location_id' => $location1->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ],
            [
                'return_item_id' => $return->returnItems[1]->id,
                'location_id' => $location2->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('POST', route('api.return.receive', $return->id), $data);

        $updatedProductLocation1 = LocationProduct::where('location_id', $location1->id)->where('product_id', $product1->id)->first();

        $updatedProductLocation2 = LocationProduct::where('location_id', $location2->id)->where('product_id', $product2->id)->first();

        $this->assertEquals(($productLocation1->quantity_on_hand + $data[0]['quantity_received']), $updatedProductLocation1->quantity_on_hand);

        $this->assertEquals(($productLocation2->quantity_on_hand + $data[1]['quantity_received']), $updatedProductLocation2->quantity_on_hand);

        $updatedProduct1 = Product::find($product1->id);

        $updatedProduct2 = Product::find($product2->id);

        $this->assertEquals(($product1->quantity_on_hand + $data[0]['quantity_received']), $updatedProduct1->quantity_on_hand);

        $this->assertEquals(($product2->quantity_on_hand + $data[1]['quantity_received']), $updatedProduct2->quantity_on_hand);
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

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $dataForStore = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $returns = app()->return->storeBatch($request);

        foreach ($returns as $key => $return) {
            $return->requested_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $return->expected_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $return->received_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $return->approved = $this->faker->numberBetween(0, 1);
            $return->notes = $this->faker->text;
            $return->reason = $this->faker->text;

            $return->save();


            $response = $this->actingAs($user, 'api')->json('GET', route('api.return.history', $return->id));

            foreach ($response->json()['data'] as $res) {
                $this->assertEquals($res['revisionable_type'], Return_::class);
                $this->assertEquals($res['revisionable_id'], $return->id);

                $key = $res['key'];

                $return = Return_::find($res['revisionable_id']);

                $this->assertNotEquals($res['old_value'], $return->$key);
                $this->assertEquals($res['new_value'], $return->$key);
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

        $dataForOrder = $this->getOrderRequestData($customer, $orderStatus, $product1, $product2);

        $request = OrderStoreRequest::make($dataForOrder);

        $order = app()->order->store($request);

        $dataForStore = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $returns = app()->return->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        foreach ($returns as $key => $return) {
            foreach ($return->returnItems as $item) {
                $item->quantity = $this->faker->numberBetween(1, 9);
                $item->save();

                $response = $this->actingAs($user, 'api')->json('GET', route('api.return.itemHistory', $item->id));

                foreach ($response->json()['data'] as $res) {
                    $this->assertEquals($res['revisionable_type'], ReturnItem::class);
                    $this->assertEquals($res['revisionable_id'], $item->id);

                    $key = $res['key'];

                    $item = ReturnItem::find($res['revisionable_id']);

                    $this->assertNotEquals($res['old_value'], $item->$key);
                    $this->assertEquals($res['new_value'], $item->$key);
                }
            }
        }

        $data = $this->getReturnRequestData($customer, $product1, $product2, $order);

        $request = StoreRequest::make($data);

        $return = app()->return->store($request);

        $returnResource = (new ReturnResource($return))->resolve();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.return.filter', $data));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($returnResource, $res));
        }
    }

    private function getReturnRequestData($customer, $product1, $product2, $order)
    {
        return [
            "order_id" => $order->id,
            "number" => str_random(12),
            "requested_at" => date('Y-m-d H:i:s'),
            "expected_at" => date('Y-m-d H:i:s'),
            "received_at" => date('Y-m-d H:i:s'),
            "approved" => $this->faker->numberBetween(0, 1),
            "notes" => $this->faker->text,
            "reason" => $this->faker->text,
            "return_items" => [
                [
                    "product_id" => $product1->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_received" => 0
                ],
                [
                    "product_id" => $product2->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_received" => 0
                ]
            ]
        ];
    }

    private function getOrderRequestData($customer, $orderStatus, $product1, $product2)
    {
        return [
            "customer_id" => $customer->id,
            "order_status_id" => $orderStatus->id,
            "number" => str_random(12),
            "ordered_at" => date('Y-m-d H:i:s'),
            "required_shipping_date_at" => date('Y-m-d H:i:s'),
            "shipping_date_before_at" => date('Y-m-d H:i:s'),
            "priority" => $this->faker->numberBetween(0, 5),
            'notes' => $this->faker->text,
            'tags' => '',
            "order_items" => [
                [
                    "product_id" => $product1->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_shipped" => 0
                ],
                [
                    "product_id" => $product2->id,
                    "quantity" => $this->faker->numberBetween(1, 9),
                    "quantity_shipped" => 0
                ]
            ],
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
    }
}
