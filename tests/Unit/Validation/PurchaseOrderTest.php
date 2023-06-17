<?php

namespace Tests\Unit\Validation;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\PurchaseOrder\StoreRequest;
use App\Http\Requests\PurchaseOrder\StoreBatchRequest;
use App\Http\Resources\PurchaseOrderItemResource;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

	public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $adminUser = $this->createAdministrator();

        $customer = $this->createCustomer();

        $supplier = $this->createSupplier($customer);

        $warehouse = $this->createWarehouse($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $purchaseOrderStatus = $this->createPurchaseOrderStatus($customer);

        $data = [
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus),
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus)
        ];

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.purchase_order.store'), $data);

        $response->assertJsonMissingValidationErrors();
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = $this->createSupplier($customer);

        $warehouse = $this->createWarehouse($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $purchaseOrderStatus = $this->createPurchaseOrderStatus($customer);

        $dataForStore = [
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus),
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $purchaseOrders = app()->purchaseOrder->storeBatch($request);

        $data = [];

        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $data[$key] = [
                "customer_id" => $customer->id,
                "supplier_id" => $supplier->id,
                "purchase_order_status_id" => $purchaseOrderStatus->id,
                "warehouse_id" => $warehouse->id,
                "number" => $purchaseOrder->number,
                "ordered_at" => date('Y-m-d H:i:s'),
                "expected_at" => date('Y-m-d H:i:s'),
                "delivered_at" => date('Y-m-d H:i:s'),
                "priority" => $this->faker->numberBetween(0, 5),
                "notes" => $this->faker->text,
                'tags' => ''
            ];

            foreach ($purchaseOrder->purchaseOrderItems as $item) {
                $data[$key]["purchase_order_items"][] = [
                    "purchase_order_item_id" => $item->id,
                    "product_id" => $item->product_id,
                    "quantity" => $this->faker->randomNumber(1),
                    "quantity_received" => 0
                ];
            }
        }

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.purchase_order.update'), $data);

        $response->assertJsonMissingValidationErrors();
    }

    public function testReceive()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $adminUser = $this->createAdministrator();

        $customer = $this->createCustomer();

        $supplier = $this->createSupplier($customer);

        $warehouse = $this->createWarehouse($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $purchaseOrderStatus = $this->createPurchaseOrderStatus($customer);

        $data = $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus);

        $request = StoreRequest::make($data);

        $purchaseOrder = app()->purchaseOrder->store($request);

        $purchaseOrderItemResource = (new PurchaseOrderItemResource($purchaseOrder->purchaseOrderItems[0]))->resolve();

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $this->createLocationProduct($location1, $product1);

        $this->createLocationProduct($location2, $product2);

        $data = [
            [
                'purchase_order_item_id' => $purchaseOrder->purchaseOrderItems[0]->id,
                'location_id' => $location1->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ],
            [
                'purchase_order_item_id' => $purchaseOrder->purchaseOrderItems[1]->id,
                'location_id' => $location2->id,
                'quantity_received' => $this->faker->numberBetween(1, 9),
            ]
        ];

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.purchase_order.receive', $purchaseOrder->id), $data);

        $response->assertJsonMissingValidationErrors();
    }

    private function getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus){
        return [
            "customer_id" => $customer->id,
            "purchase_order_status_id" => $purchaseOrderStatus->id,
            "warehouse_id" => $warehouse->id,
            "supplier_id" => $supplier->id,
            "number" => str_random(12),
            "ordered_at" => date('Y-m-d H:i:s'),
            "expected_at" => date('Y-m-d H:i:s'),
            "delivered_at" => date('Y-m-d H:i:s'),
            "priority" => $this->faker->numberBetween(0, 5),
            "notes" => $this->faker->text,
            'tags' => '',
            "purchase_order_items" => [
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
}
