<?php

namespace Tests\Unit;

use App\Components\InventoryLogComponent;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Models\LocationProduct;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class LocationTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = $this->createWarehouse($customer);

        $location = factory(Location::class)->create(['warehouse_id' => $warehouse->id]);

        $locationResource = (new LocationResource($location))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.location.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($locationResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = $this->createWarehouse($customer);

        $product = $this->createProduct($customer);

        $location = factory(Location::class)->create(['warehouse_id' => $warehouse->id]);

        $locationResource = (new LocationResource($location))->resolve();

        $data = [
            [
                'warehouse_id' => $warehouse->id,
                'name' => $this->faker->name,
                'pickable' => $this->faker->numberBetween(0, 1),
                "location_product" => [
                    [
                        "product_id" => $product->id,
                        "quantity_on_hand" => $this->faker->numberBetween(1, 9)
                    ],
                ]
            ],
            [
                'warehouse_id' => $warehouse->id,
                'name' => $this->faker->name,
                'pickable' => $this->faker->numberBetween(0, 1),
                "location_product" => [
                    [
                        "product_id" => $product->id,
                        "quantity_on_hand" => $this->faker->numberBetween(1, 9)
                    ],
                ]
            ]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.location.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.location.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.location.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($locationResource, $res));

            $location = Location::where('id', $res['id'])->first();

            $this->assertTrue($location->warehouse->customer->users->contains('id', $user->id));

            $this->assertFalse($location->warehouse->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = $this->createWarehouse($customer);

        $location = factory(Location::class)->create(['warehouse_id' => $warehouse->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $location));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $location));

        $locationResource = (new LocationResource($location))->resolve();

        $data = [
            [
                'id' => $location->id,
                'warehouse_id' => $warehouse->id,
                'name' => $this->faker->name,
                'pickable' => $this->faker->numberBetween(0, 1)
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.location.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($locationResource, $res));

            $location = Location::where('id', $res['id'])->first();

            $this->assertTrue($location->warehouse->customer->users->contains('id', $user->id));

            $this->assertFalse($location->warehouse->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = $this->createWarehouse($customer);

        $location = factory(Location::class)->create(['warehouse_id' => $warehouse->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $location));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $location));

        $data = [ ['id' => $location->id, 'warehouse_id' => $warehouse->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.location.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('locations', ['name' => $value['name']]);
        }
    }

    public function testTransferQuantityCalculation()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $product = $this->createProduct($customer);

        $warehouse = $this->createWarehouse($customer);

        $location1 = $this->createLocation($warehouse);

        $location2 = $this->createLocation($warehouse);

        $productLocation1 = $this->createLocationProduct($location1, $product);

        $productLocation2 = $this->createLocationProduct($location2, $product);

        $quantity = $this->faker->numberBetween(1, 9);

        app('inventoryLog')->adjustInventory($location1, $product, $quantity, InventoryLogComponent::OPERATION_TYPE_TRANSFER, $location2);

        $updatedProductLocation1 = LocationProduct::where('location_id', $location1->id)->where('product_id', $product->id)->first();

        $updatedProductLocation2 = LocationProduct::where('location_id', $location2->id)->where('product_id', $product->id)->first();

        $this->assertEquals(($productLocation1->quantity_on_hand - $updatedProductLocation1->quantity_on_hand), ($updatedProductLocation2->quantity_on_hand - $productLocation2->quantity_on_hand));
    }
}
