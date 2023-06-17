<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\OrderStatusResource;
use App\Models\User;
use App\Models\UserRole;
use App\Models\CustomerUserRole;
use App\Models\Customer;
use App\Models\OrderStatus;
use App\Models\ContactInformation;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $orderStatus = factory(OrderStatus::class)->create(['customer_id' => $customer->id]);

        $orderStatusResource = (new OrderStatusResource($orderStatus))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.order_status.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($orderStatusResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $orderStatus = factory(OrderStatus::class)->create(['customer_id' => $customer->id]);

        $orderStatusResource = (new OrderStatusResource($orderStatus))->resolve();

        $data = [
            [
                'name' => str_random(4),
                'customer_id' => $customer->id
            ],
            [
                'name' => str_random(4),
                'customer_id' => $customer->id
            ]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.order_status.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.order_status.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.order_status.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($orderStatusResource, $res));

            $orderStatus = OrderStatus::where('id', $res['id'])->first();

            $this->assertTrue($orderStatus->customer->users->contains('id', $user->id));

            $this->assertFalse($orderStatus->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $orderStatus = factory(OrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $orderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $orderStatus));

        $orderStatusResource = (new OrderStatusResource($orderStatus))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.order_status.show', $orderStatus->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($orderStatusResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $orderStatus = factory(OrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $orderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $orderStatus));

        $orderStatusResource = (new OrderStatusResource($orderStatus))->resolve();

        $data = [
            [
                'id' => $orderStatus->id,
                'customer_id' => $customer->id,
                'name' => str_random(4),
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.order_status.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($orderStatusResource, $res));

            $orderStatus = OrderStatus::where('id', $res['id'])->first();

            $this->assertTrue($orderStatus->customer->users->contains('id', $user->id));

            $this->assertFalse($orderStatus->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $orderStatus = factory(OrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $orderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $orderStatus));

        $data = [ ['id' => $orderStatus->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.order_status.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('order_statuses', ['id' => $value['id']]);
        }
    }
}
