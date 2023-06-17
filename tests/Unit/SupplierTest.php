<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class SupplierTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);

        $supplierResource = (new SupplierResource($supplier))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.supplier.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($supplierResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);

        $supplierResource = (new SupplierResource($supplier))->resolve();

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

        $this->actingAs($user, 'api')->json('POST', route('api.supplier.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.supplier.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.supplier.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($supplierResource, $res));

            $supplier = Supplier::where('id', $res['id'])->first();

            $this->assertTrue($supplier->customer->users->contains('id', $user->id));

            $this->assertFalse($supplier->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $supplier));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $supplier));

        $supplierResource = (new SupplierResource($supplier))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.supplier.show', $supplier->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($supplierResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);;

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $supplier));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $supplier));

        $supplierResource = (new SupplierResource($supplier))->resolve();

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
                'id' => $supplier->id,
                'customer_id' => $customer->id,
                'contact_information' => $contact_information_data
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.supplier.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($supplierResource, $res));

            $supplier = Supplier::where('id', $res['id'])->first();

            $this->assertTrue($supplier->customer->users->contains('id', $user->id));

            $this->assertFalse($supplier->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $supplier = Supplier::create(['customer_id' => $customer->id]);

        $this->createContactInformation($supplier);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $supplier));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $supplier));

        $data = [ ['id' => $supplier->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.supplier.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('suppliers', ['id' => $value['id']]);
        }
    }
}
