<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class WarehouseTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        $warehouseResource = (new WarehouseResource($warehouse))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.warehouse.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($warehouseResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        $warehouseResource = (new WarehouseResource($warehouse))->resolve();

        $contact_information_data = [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country_id' => 1
        ];

        $data = [
            [
                'customer_id' => $customer->id,
                'contact_information' => $contact_information_data
            ],
            [
                'customer_id' => $customer->id,
                'contact_information' => $contact_information_data
            ]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.warehouse.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.warehouse.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.warehouse.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($warehouseResource, $res));

            $warehouse = Warehouse::where('id', $res['id'])->first();

            $this->assertTrue($warehouse->customer->users->contains('id', $user->id));

            $this->assertFalse($warehouse->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $warehouse));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $warehouse));

        $warehouseResource = (new WarehouseResource($warehouse))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.warehouse.show', $warehouse->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($warehouseResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $warehouse));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $warehouse));

        $warehouseResource = (new WarehouseResource($warehouse))->resolve();

        $contact_information_data = [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country_id' => 1
        ];

        $data = [
            [
                'id' => $warehouse->id,
                'customer_id' => $customer->id,
                'contact_information' => $contact_information_data
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.warehouse.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($warehouseResource, $res));

            $warehouse = Warehouse::where('id', $res['id'])->first();

            $this->assertTrue($warehouse->customer->users->contains('id', $user->id));

            $this->assertFalse($warehouse->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create(['customer_id' => $customer->id]);

        $this->createContactInformation($warehouse);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $warehouse));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $warehouse));

        $data = [ ['id' => $warehouse->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.warehouse.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('warehouses', ['id' => $value['id']]);
        }
    }
}
