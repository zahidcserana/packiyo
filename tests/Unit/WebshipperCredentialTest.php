<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\WebshipperCredentialResource;
use App\Models\WebshipperCredential;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class WebshipperCredentialTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $credential = factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $resource = (new WebshipperCredentialResource($credential))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.webshipper_credential.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($resource, $res));
        }
    }

    public function testStore()
    {
        $this->markTestSkipped();

        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $credential = factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $resource = (new WebshipperCredentialResource($credential))->resolve();

        $data = [
            [
                'customer_id' => $customer->id,
                'api_base_url' => $this->faker->url,
                'api_key' => str_random(60)
            ]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.webshipper_credential.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.webshipper_credential.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.webshipper_credential.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($resource, $res));

            $credential = WebshipperCredential::where('id', $res['id'])->first();

            $this->assertTrue($credential->customer->users->contains('id', $user->id));

            $this->assertFalse($credential->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $credential = factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $credential));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $credential));

        $resource = (new WebshipperCredentialResource($credential))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.webshipper_credential.show', $credential->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($resource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $credential = factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $credential));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $credential));

        $resource = (new WebshipperCredentialResource($credential))->resolve();

        $data = [
            [
                'id' => $credential->id,
                'customer_id' => $customer->id,
                'api_base_url' => $this->faker->url,
                'api_key' => str_random(60),
            ],
            [
                'id' => $credential->id,
                'customer_id' => $customer->id,
                'api_base_url' => $this->faker->url,
                'api_key' => str_random(60),
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.webshipper_credential.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($resource, $res));

            $credential = WebshipperCredential::where('id', $res['id'])->first();

            $this->assertTrue($credential->customer->users->contains('id', $user->id));

            $this->assertFalse($credential->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $credential = factory(WebshipperCredential::class)->create([
            'customer_id' => $customer->id,
            'order_channel_id' => 1
        ]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $credential));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $credential));

        $data = [ ['id' => $credential->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.webshipper_credential.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('webshipper_credentials', ['id' => $value['id']]);
        }
    }
}
