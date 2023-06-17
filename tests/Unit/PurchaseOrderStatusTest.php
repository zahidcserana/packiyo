<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\PurchaseOrderStatusResource;
use App\Models\PurchaseOrderStatus;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class PurchaseOrderStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $purchaseOrderStatus = factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);

        $purchaseOrderStatusResource = (new PurchaseOrderStatusResource($purchaseOrderStatus))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.purchase_order_status.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderStatusResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $purchaseOrderStatus = factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);

        $purchaseOrderStatusResource = (new PurchaseOrderStatusResource($purchaseOrderStatus))->resolve();

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

        $this->actingAs($user, 'api')->json('POST', route('api.purchase_order_status.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.purchase_order_status.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.purchase_order_status.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderStatusResource, $res));

            $purchaseOrderStatus = PurchaseOrderStatus::where('id', $res['id'])->first();

            $this->assertTrue($purchaseOrderStatus->customer->users->contains('id', $user->id));

            $this->assertFalse($purchaseOrderStatus->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $purchaseOrderStatus = factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $purchaseOrderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $purchaseOrderStatus));

        $purchaseOrderStatusResource = (new PurchaseOrderStatusResource($purchaseOrderStatus))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.purchase_order_status.show', $purchaseOrderStatus->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($purchaseOrderStatusResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $purchaseOrderStatus = factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $purchaseOrderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $purchaseOrderStatus));

        $purchaseOrderStatusResource = (new PurchaseOrderStatusResource($purchaseOrderStatus))->resolve();

        $data = [
            [
                'id' => $purchaseOrderStatus->id,
                'customer_id' => $customer->id,
                'name' => str_random(4),
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.purchase_order_status.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($purchaseOrderStatusResource, $res));

            $purchaseOrderStatus = PurchaseOrderStatus::where('id', $res['id'])->first();

            $this->assertTrue($purchaseOrderStatus->customer->users->contains('id', $user->id));

            $this->assertFalse($purchaseOrderStatus->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $purchaseOrderStatus = factory(PurchaseOrderStatus::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $purchaseOrderStatus));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $purchaseOrderStatus));

        $data = [ ['id' => $purchaseOrderStatus->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.purchase_order_status.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('purchase_order_statuses', ['id' => $value['id']]);
        }
    }
}
