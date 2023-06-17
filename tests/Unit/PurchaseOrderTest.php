<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\PurchaseOrder\DestroyBatchRequest;
use App\Http\Requests\PurchaseOrder\StoreBatchRequest;
use App\Http\Requests\PurchaseOrder\StoreRequest;
use App\Http\Requests\PurchaseOrder\UpdateBatchRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\PurchaseOrderItemResource;
use App\Models\Product;
use App\Models\LocationProduct;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase, UnitTestSetup, WithFaker;

    public function testIndex()
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

        $purchaseOrderResource = (new PurchaseOrderResource($purchaseOrder))->resolve();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.purchase_order.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [],
                'links' => [],
                'meta' => []
            ]
        );

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

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

        $request = StoreBatchRequest::make($data);

        $purchaseOrders = app()->purchaseOrder->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $this->assertInstanceOf(PurchaseOrder::class, $purchaseOrder);

            $this->assertEquals($data[$key]['number'], $purchaseOrder->number);

            foreach ($purchaseOrder->purchaseOrderItems as $item) {
                $this->assertInstanceOf(PurchaseOrderItem::class, $item);

                $this->assertEquals($item->purchase_order_id, $purchaseOrder->id);
            }

            $this->assertTrue($purchaseOrder->customer->users->contains('id', $user->id));

            $this->assertFalse($purchaseOrder->customer->users->contains('id', $guestUser->id));
        }

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($user, 'api')->json('POST', route('api.purchase_order.store'), $data)->assertStatus(200);

        $data = $this->regenerateUniqueNumber($data);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.purchase_order.store'), $data)->assertStatus(403);
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

        $product3 = $this->createProduct($customer);

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
                "warehouse_id" => $warehouse->id,
                "supplier_id" => $supplier->id,
                "purchase_order_status_id" => $purchaseOrderStatus->id,
                "number" => $purchaseOrder->number,
                "ordered_at" => date('Y-m-d H:i:s'),
                "expected_at" => date('Y-m-d H:i:s'),
                "delivered_at" => date('Y-m-d H:i:s'),
                "priority" => $this->faker->numberBetween(0, 5),
                "notes" => $this->faker->text,
            ];

            foreach ($purchaseOrder->purchaseOrderItems as $item) {
                $data[$key]["purchase_order_items"][] = [
                    "purchase_order_item_id" => $item->id,
                    "product_id" => $item->product_id,
                    "quantity" => $this->faker->randomNumber(1),
                    "quantity_received" => 0
                ];
            }

            $data[$key]["purchase_order_items"][] = [
                "product_id" => $product3->id,
                "quantity" => $this->faker->randomNumber(1),
                "quantity_received" => $this->faker->randomNumber(1)
            ];
        }

        $request = UpdateBatchRequest::make($data);

        $purchaseOrders = app()->purchaseOrder->updateBatch($request);

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $this->assertInstanceOf(PurchaseOrder::class, $purchaseOrder);

            $this->assertEquals($data[$key]['number'], $purchaseOrder->number);

            $this->assertEquals($data[$key]['ordered_at'], $purchaseOrder->ordered_at);

            $this->assertTrue($purchaseOrder->customer->users->contains('id', $user->id));

            $this->assertFalse($purchaseOrder->customer->users->contains('id', $guestUser->id));

            $this->assertTrue($user->can('update', $purchaseOrder));

            $this->assertFalse($guestUser->can('update', $purchaseOrder));
        }
    }

    public function testDestroy()
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

        $user = $this->createCustomerUser($customer);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $data = [];

        foreach ($purchaseOrders as $key => $purchaseOrder) {
            $this->assertTrue($user->can('delete', $purchaseOrder));

            $this->assertFalse($guestUser->can('delete', $purchaseOrder));

            $data[$key] = [
                "id" => $purchaseOrder->id,
            ];
        }

        $request = DestroyBatchRequest::make($data);

        $ids = app()->purchaseOrder->destroyBatch($request);

        foreach ($ids as $key => $value) {
            $this->assertSoftDeleted('purchase_orders', ['id' => $value['id']]);
        }
    }

    public function testReceive()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

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

        $response = $this->actingAs($user, 'api')->json('POST', route('api.purchase_order.receive', $purchaseOrder->id), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderItemResource, $res));
        }

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.purchase_order.receive', $purchaseOrder->id), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.purchase_order.receive', $purchaseOrder->id), $data)->assertStatus(403);
    }

    public function testReceiveQuantityCalculation()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $supplier = $this->createSupplier($customer);

        $warehouse = $this->createWarehouse($customer);

        $product1 = $this->createProduct($customer);

        $product2 = $this->createProduct($customer);

        $purchaseOrderStatus = $this->createPurchaseOrderStatus($customer);

        $data = $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus);

        $request = StoreRequest::make($data);

        $purchaseOrder = app()->purchaseOrder->store($request);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $productLocation1 = $this->createLocationProduct($location1, $product1);

        $productLocation2 = $this->createLocationProduct($location2, $product2);

        $data = [
            [
                'purchase_order_item_id' => $purchaseOrder->purchaseOrderItems[0]->id,
                'location_id' => $location1->id,
                'quantity_received' => $this->faker->numberBetween(1, 9)
            ],
            [
                'purchase_order_item_id' => $purchaseOrder->purchaseOrderItems[1]->id,
                'location_id' => $location2->id,
                'quantity_received' => $this->faker->numberBetween(1, 9)
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('POST', route('api.purchase_order.receive', $purchaseOrder->id), $data);

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

        foreach ($purchaseOrders as $purchaseOrder) {
            $purchaseOrder->ordered_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $purchaseOrder->expected_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $purchaseOrder->delivered_at = $this->faker->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s');
            $purchaseOrder->priority = $this->faker->numberBetween(0, 5);
            $purchaseOrder->notes = $this->faker->text;

            $purchaseOrder->save();

            $response = $this->actingAs($user, 'api')->json('GET', route('api.purchase_order.history', $purchaseOrder->id));

            foreach ($response->json()['data'] as $res) {
                $this->assertEquals($res['revisionable_type'], PurchaseOrder::class);
                $this->assertEquals($res['revisionable_id'], $purchaseOrder->id);

                $key = $res['key'];

                $purchaseOrder = PurchaseOrder::find($res['revisionable_id']);

                $this->assertNotEquals($res['old_value'], $purchaseOrder->$key);
                $this->assertEquals($res['new_value'], $purchaseOrder->$key);
            }
        }
    }

    public function testFilter()
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

        $dataForStore = [
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus),
            $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus)
        ];

        $request = StoreBatchRequest::make($dataForStore);

        $purchaseOrders = app()->purchaseOrder->storeBatch($request);

        $user = $this->createCustomerUser($customer);

        foreach ($purchaseOrders as $purchaseOrder) {
            foreach ($purchaseOrder->purchaseOrderItems as $item) {
                $item->quantity = $this->faker->numberBetween(1, 9);
                $item->save();

                $response = $this->actingAs($user, 'api')->json('GET', route('api.purchase_order.itemHistory', $item->id));

                foreach ($response->json()['data'] as $res) {
                    $this->assertEquals($res['revisionable_type'], PurchaseOrderItem::class);
                    $this->assertEquals($res['revisionable_id'], $item->id);

                    $key = $res['key'];

                    $item = PurchaseOrderItem::find($res['revisionable_id']);

                    $this->assertNotEquals($res['old_value'], $item->$key);
                    $this->assertEquals($res['new_value'], $item->$key);
                }
            }
        }

        $data = $this->getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus);

        $request = StoreRequest::make($data);

        $purchaseOrder = app()->purchaseOrder->store($request);

        $purchaseOrderResource = (new PurchaseOrderResource($purchaseOrder))->resolve();

        $data = [
            'from_date_created' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_created' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'from_date_updated' => $this->faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'to_date_updated' => $this->faker->dateTimeBetween('now', '+15 days')->format('Y-m-d')
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.purchase_order.filter', $data));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderResource, $res));
        }
    }

    private function getPurchaseOrderRequestData($customer, $warehouse, $supplier, $product1, $product2, $purchaseOrderStatus){
        $data = [
            "customer_id" => $customer->id,
            "warehouse_id" => $warehouse->id,
            "supplier_id" => $supplier->id,
            "purchase_order_status_id" => $purchaseOrderStatus->id,
            "number" => str_random(12),
            "ordered_at" => date('Y-m-d H:i:s'),
            "expected_at" => date('Y-m-d H:i:s'),
            "delivered_at" => date('Y-m-d H:i:s'),
            "priority" => $this->faker->numberBetween(0, 5),
            "notes" => $this->faker->text,
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

        return $data;
    }
}
