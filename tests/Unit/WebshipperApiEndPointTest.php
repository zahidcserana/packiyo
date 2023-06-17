<?php

namespace Tests\Unit;

use App\Components\InventoryLogComponent;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\WebshipperCredential;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class WebshipperApiEndPointTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testCarriers()
    {
        $this->markTestSkipped();

        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.webshipper.carriers', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertArrayHasKey('id', $res);
            $this->assertArrayHasKey('alias', $res);
            $this->assertArrayHasKey('services', $res);
            $this->assertIsArray($res['services']);
        }
    }

    public function testShipment()
    {
        $this->markTestSkipped();

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

        foreach ($order->orderItems as $orderItem) {
            $location = $this->createLocation($warehouse);
            $quantity = $this->faker->numberBetween(1, 9);

            $shipmentItem = ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'product_id' => $orderItem->product_id,
                'quantity' => $quantity
            ]);

            app('inventoryLog')->adjustInventory($location, $orderItem->product, -$quantity, InventoryLogComponent::OPERATION_TYPE_SHIP, $shipment);

            $orderItem->quantity_shipped += $quantity;
            $orderItem->save();
        }

        factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $carriers = app()->webshipperShipping->carriers($customer);

        if (count($carriers) == 0) {
            return true;
        }

        $carrierId = reset($carriers)['id'];

        $webshipperShipment = app()->webshipperShipping->processShipment($shipment, $carrierId);

        $this->assertEquals($webshipperShipment['type'], 'shipments');
        $this->assertEquals($webshipperShipment['attributes']['reference'], $shipment->order->number);

        foreach ($webshipperShipment['attributes']['packages'][0]['customs_lines'] as $key => $item) {
            $this->assertEquals($item['sku'], $shipment->shipmentItems[$key]->product->sku);
            $this->assertEquals($item['description'], $shipment->shipmentItems[$key]->product->name);
            $this->assertEquals($item['unit_price'], $shipment->shipmentItems[$key]->product->price);
            $this->assertEquals($item['quantity'], $shipment->shipmentItems[$key]->quantity);
        }

        $this->assertEquals($webshipperShipment['attributes']['delivery_address']['att_contact'], $shipment->contactInformation->name);
        $this->assertEquals($webshipperShipment['attributes']['delivery_address']['company_name'], $shipment->contactInformation->company_name);
        $this->assertEquals($webshipperShipment['attributes']['delivery_address']['zip'], $shipment->contactInformation->zip);
        $this->assertEquals($webshipperShipment['attributes']['delivery_address']['email'], $shipment->contactInformation->email);
        $this->assertEquals($webshipperShipment['attributes']['delivery_address']['city'], $shipment->contactInformation->city);

        $this->assertEquals($webshipperShipment['attributes']['sender_address']['att_contact'], $customer->contactInformation->name);
        $this->assertEquals($webshipperShipment['attributes']['sender_address']['company_name'], $customer->contactInformation->company_name);
        $this->assertEquals($webshipperShipment['attributes']['sender_address']['zip'], $customer->contactInformation->zip);
        $this->assertEquals($webshipperShipment['attributes']['sender_address']['email'], $customer->contactInformation->email);
        $this->assertEquals($webshipperShipment['attributes']['sender_address']['city'], $customer->contactInformation->city);

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
