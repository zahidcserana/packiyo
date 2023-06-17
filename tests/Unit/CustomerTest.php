<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\UserResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\WarehouseResource;
use App\Http\Resources\ProductResource;
use App\Models\CustomerUserRole;
use App\Models\Warehouse;
use App\Models\Task;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class CustomerTest extends TestCase
{
    use RefreshDatabase, UnitTestSetup, WithFaker;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $customerResource = (new CustomerResource($customer))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.customer.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($customerResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $customerResource = (new CustomerResource($customer))->resolve();

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
                'contact_information' => $contact_information_data
            ],
            [
                'contact_information' => $contact_information_data
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.customer.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($customerResource, $res));
        }

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $this->actingAs($customerAdmimistrator, 'api')->json('POST', route('api.customer.store'), $data)->assertStatus(403);
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $customerResource = (new CustomerResource($customer))->resolve();

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
            ['id' => $customer->id, 'contact_information' => $contact_information_data]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('PUT', route('api.customer.update'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('PUT', route('api.customer.update'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.customer.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($customerResource, $res));

            $this->assertTrue($customer->users->contains('id', $user->id));

            $this->assertFalse($customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $this->assertFalse($customerAdmimistrator->can('delete', $customer));

        $data = [ ['id' => $customer->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.customer.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('customers', ['id' => $value['id']]);
        }
    }

    public function testUsers()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $userResource = (new UserResource($user))->resolve();

        $response = $this->actingAs($user, 'api')->json('GET', route('api.customer.users', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($customer->hasUser($res['id']));

            $this->assertEmpty(array_diff_key($userResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.customer.users', $customer->id))->assertStatus(403);
    }

    public function testTasks()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $taskType = $this->createTaskType($customer);

        $task = factory(Task::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'task_type_id' => $taskType->id
        ]);

        $taskResource = (new TaskResource($task))->resolve();

        $response = $this->actingAs($user, 'api')->json('GET', route('api.customer.tasks', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($customer->tasks->contains('id', $res['id']));

            $this->assertEmpty(array_diff_key($taskResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.customer.tasks', $customer->id))->assertStatus(403);
    }

    public function testWarehouses()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $warehouse = Warehouse::create([
            'customer_id' => $customer->id
        ]);

        $this->createContactInformation($warehouse);

        $warehouseResource = (new WarehouseResource($warehouse))->resolve();

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.customer.warehouses', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($customer->warehouses->contains('id', $res['id']));

            $this->assertEmpty(array_diff_key($warehouseResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.customer.warehouses', $customer->id))->assertStatus(403);
    }

    public function testProducts()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $product = $this->createProduct($customer);

        $productResource = (new ProductResource($product))->resolve();

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.customer.products', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($customer->products->contains('id', $res['id']));

            $this->assertEmpty(array_diff_key($productResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.customer.products', $customer->id))->assertStatus(403);
    }

    public function testListUsers()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.customer.list_users', $customer->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($customer->hasUser($res['user_id']));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.customer.list_users', $customer->id))->assertStatus(403);
    }

    public function testUpdateUsers()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $newUser = $this->createUser();

        $data = [
            "new_user_id" => $newUser->id,
            "new_user_role_id" => CustomerUserRole::ROLE_CUSTOMER_MEMBER,
            "customer_user" => [
                [
                    "user_id" => $user->id,
                    "role_id" => CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR
                ]
            ]
        ];

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $response = $this->actingAs($customerAdmimistrator, 'api')->json('PUT', route('api.customer.update_users', $customer->id), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertTrue($customer->hasUser($res['user_id']));
        }

        $memberUser = $this->createCustomerUser($customer);

        $this->actingAs($memberUser, 'api')->json('PUT', route('api.customer.update_users', $customer->id), $data)->assertStatus(403);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('PUT', route('api.customer.update_users', $customer->id), $data)->assertStatus(403);
    }

    public function testDetachUser()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $response = $this->actingAs($customerAdmimistrator, 'api')->json('DELETE', route('api.customer.detach_user', [$customer->id, $user->id]));

        $response->assertStatus(200);

        $this->assertEquals($response->json(), 1);

        $memberUser = $this->createCustomerUser($customer);

        $this->actingAs($memberUser, 'api')->json('DELETE', route('api.customer.detach_user', [$customer->id, $user->id]))->assertStatus(403);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('DELETE', route('api.customer.detach_user', [$customer->id, $user->id]))->assertStatus(403);
    }
}
