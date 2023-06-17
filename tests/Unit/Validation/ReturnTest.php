<?php

namespace Tests\Unit\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Order\StoreRequest as OrderStoreRequest;
use App\Http\Requests\Return_\StoreRequest;
use App\Http\Requests\Return_\StoreBatchRequest;
use App\Http\Resources\ReturnItemResource;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class ReturnTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

	public function testStore()
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

        $data = [
            $this->getReturnRequestData($customer, $product1, $product2, $order),
            $this->getReturnRequestData($customer, $product1, $product2, $order)
        ];

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.return.store'), $data);

        $response->assertJsonMissingValidationErrors();
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
                    "quantity_received" => 0
                ];
            }
        }

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.return.update'), $data);

        $response->assertJsonMissingValidationErrors();
    }

    public function testReceive()
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

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.return.receive', $return->id), $data);

        $response->assertJsonMissingValidationErrors();
    }

    private function getReturnRequestData($customer, $product1, $product2, $order){
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

    private function getOrderRequestData($customer, $orderStatus, $product1, $product2){
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
