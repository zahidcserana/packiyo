<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\WebhookResource;
use App\Models\Webhook;
use App\Models\Task;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class WebhookTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $webhook = factory(Webhook::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'object_type' => Task::class,
            'operation' => Webhook::OPERATION_TYPE_STORE
        ]);

        $webhookResource = (new WebhookResource($webhook))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.webhook.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($webhookResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $webhook = factory(Webhook::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'object_type' => Task::class,
            'operation' => Webhook::OPERATION_TYPE_STORE
        ]);

        $webhookResource = (new WebhookResource($webhook))->resolve();

        $data = [
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'name' => $this->faker->word,
                'object_type' => Task::class,
                'operation' => Webhook::OPERATION_TYPE_STORE,
                'url' => $this->faker->url,
                'secret_key' => $this->faker->word,
            ]
        ];

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.webhook.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.webhook.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($webhookResource, $res));

            $webhook = Webhook::where('id', $res['id'])->first();

            $this->assertTrue($webhook->customer->users->contains('id', $user->id));

            $this->assertFalse($webhook->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $webhook = factory(Webhook::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'object_type' => Task::class,
            'operation' => Webhook::OPERATION_TYPE_STORE
        ]);

        $this->assertTrue($user->can('view', $webhook));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $webhook));

        $webhookResource = (new WebhookResource($webhook))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.webhook.show', $webhook->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($webhookResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $webhook = factory(Webhook::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'object_type' => Task::class,
            'operation' => Webhook::OPERATION_TYPE_STORE
        ]);

        $this->assertTrue($user->can('update', $webhook));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $webhook));

        $webhookResource = (new WebhookResource($webhook))->resolve();

        $data = [
            [
                'id' => $webhook->id,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'name' => $this->faker->word,
                'object_type' => Task::class,
                'operation' => Webhook::OPERATION_TYPE_UPDATE,
                'url' => $this->faker->url,
                'secret_key' => $this->faker->word
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.webhook.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($webhookResource, $res));

            $webhook = Webhook::where('id', $res['id'])->first();

            $this->assertTrue($webhook->customer->users->contains('id', $user->id));

            $this->assertFalse($webhook->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $webhook = factory(Webhook::class)->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'object_type' => Task::class,
            'operation' => Webhook::OPERATION_TYPE_STORE
        ]);

        $this->assertTrue($user->can('delete', $webhook));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $webhook));

        $data = [ ['id' => $webhook->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.webhook.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('webhooks', ['id' => $value['id']]);
        }
    }
}
