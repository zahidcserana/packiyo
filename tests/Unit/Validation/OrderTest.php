<?php

namespace Tests\Unit\Validation;

use App\Models\OrderItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Order\StoreBatchRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Models\User;
use App\Models\UserRole;
use App\Models\CustomerUserRole;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Shipment;
use App\Models\ContactInformation;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class OrderTest extends TestCase
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

        $data = [
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2),
            $this->getOrderRequestData($customer, $orderStatus, $product1, $product2)
        ];

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.order.store'), $data);

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

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.order.update'), $data);

        $response->assertJsonMissingValidationErrors();
    }

    public function testShip()
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

        $shipment = Shipment::create([
            'order_id' => $order->id
        ]);

        $this->createContactInformation($shipment);

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

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.order.ship', $order->id), $data);

        $response->assertJsonMissingValidationErrors();
    }

    private function getOrderRequestData($customer, $orderStatus, $product1, $product2){
        $data = [
            "customer_id" => $customer->id,
            "order_status_id" => $orderStatus->id,
            "number" => str_random(12),
            "ordered_at" => date('Y-m-d H:i:s'),
            "required_shipping_date_at" => date('Y-m-d H:i:s'),
            "shipping_date_before_at" => date('Y-m-d H:i:s'),
            "priority" => $this->faker->numberBetween(0, 5),
            'slip_note' => $this->faker->text,
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

        return $data;
    }
}
