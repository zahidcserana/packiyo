<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\UserResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\WebhookResource;
use App\Models\User;
use App\Models\UserRole;
use App\Models\CustomerUserRole;
use App\Models\Customer;
use App\Models\Webhook;
use App\Models\Task;
use App\Models\ContactInformation;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $testUser = $this->createCustomerUser($customer);

        $testUserResource = (new UserResource($testUser))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.user.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $testUser = $this->createCustomerUser($customer);

        $testUserResource = (new UserResource($testUser))->resolve();

        $data = [
            $this->getUserData($customer),
            $this->getUserData($customer)
        ];

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $this->actingAs($customerAdmimistrator, 'api')->json('POST', route('api.user.store'), $data)->assertStatus(200);

        $customerMember = $this->createCustomerUser($customer);

        $data = $this->regenerateUniqueEmail($data);

        $this->actingAs($customerMember, 'api')->json('POST', route('api.user.store'), $data)->assertStatus(403);

        $data = $this->regenerateUniqueEmail($data);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.user.store'), $data);

        $response->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($testUserResource, $res));

            $user = User::where('id', $res['id'])->first();

            $this->assertTrue(in_array($customerAdmimistrator->id, app()->user->getAllCustomerUserIds($user)));

            $this->assertTrue(in_array($customerMember->id, app()->user->getAllCustomerUserIds($user)));

            $this->assertFalse(in_array($guestUser->id, app()->user->getAllCustomerUserIds($user)));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $testUser = $this->createCustomerUser($customer);

        $testUserResource = (new UserResource($testUser))->resolve();

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('GET', route('api.user.show', $testUser->id))->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.user.show', $testUser->id))->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.user.show', $testUser->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($testUserResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $testUser = $this->createCustomerUser($customer);

        $testUserResource = (new UserResource($testUser))->resolve();

        $dataForUpdate = $this->getUserData($customer);

        unset($dataForUpdate['email']);
        unset($dataForUpdate['role_id']);

        $dataForUpdate['email'] = $testUser->email;

        $data = [ $dataForUpdate ];

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);
        $this->actingAs($customerAdmimistrator, 'api')->json('PUT', route('api.user.update'), $data)->assertStatus(200);

        $customerMember = $this->createCustomerUser($customer);
        $this->actingAs($customerMember, 'api')->json('PUT', route('api.user.update'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.user.update'), $data);

        $response->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($testUserResource, $res));

            $user = User::where('id', $res['id'])->first();

            $this->assertTrue(in_array($customerAdmimistrator->id, app()->user->getAllCustomerUserIds($user)));

            $this->assertTrue(in_array($customerMember->id, app()->user->getAllCustomerUserIds($user)));

            $this->assertFalse(in_array($guestUser->id, app()->user->getAllCustomerUserIds($user)));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $testUser = $this->createCustomerUser($customer);

        $customerAdmimistrator = $this->createCustomerAdminUser($customer);

        $this->assertTrue($customerAdmimistrator->can('delete', $testUser));

        $customerMember = $this->createCustomerUser($customer);

        $this->assertFalse($customerMember->can('delete', $testUser));

        $data = [
            ['email' => $testUser->email]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.user.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('users', ['email' => $value['email']]);
        }
    }

    public function testCustomers()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $customerResource = (new CustomerResource($customer))->resolve();

        $user = $this->createCustomerUser($customer);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.user.customers', $user->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($user->hasCustomer($res['id']));

            $this->assertEmpty(array_diff_key($customerResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.user.customers', $user->id))->assertStatus(403);
    }

    public function testWebhooks()
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

        $response = $this->actingAs($user, 'api')->json('GET', route('api.user.webhooks', $user->id));

        $response->assertStatus(200);

        foreach ($response->json()['data'] as $res) {
            $this->assertTrue($user->webhooks->contains('id', $res['id']));

            $this->assertEmpty(array_diff_key($webhookResource, $res));
        }

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('GET', route('api.user.webhooks', $user->id))->assertStatus(403);
    }

    public function testGetTokens()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $tokenName = $this->faker->word;


        $user->createToken($tokenName);

        $response = $this->actingAs($user, 'api')->json('GET', route('api.user.access_tokens'));

        $response->assertStatus(200);

        $this->assertEquals(count($response->json()['data']), 1);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $response = $this->actingAs($guestUser, 'api')->json('GET', route('api.user.access_tokens'));

        $response->assertStatus(200);

        $this->assertEquals(count($response->json()['data']), 0);
    }

    public function testUpdateTokens()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $tokenName = $this->faker->word;

        $data = [
            "access_token" =>
            [
                $tokenName
            ]
        ];

        $response = $this->actingAs($user, 'api')->json('PUT', route('api.user.update_access_tokens'), $data);

        $response->assertStatus(200);

        $this->assertTrue($response->json());
    }

    public function testDeleteAccessToken()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $user = $this->createCustomerUser($customer);

        $tokenName = $this->faker->word;


        $token = $user->createToken($tokenName)->token->id;

        $response = $this->actingAs($user, 'api')->json('DELETE', route('api.user.delete_access_tokens', $token));

        $response->assertStatus(200);

        $this->assertTrue($response->json());

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $token = $user->createToken($tokenName)->token->id;

        $this->actingAs($guestUser, 'api')->json('DELETE', route('api.user.delete_access_tokens', $token))->assertStatus(403);
    }

    private function getUserData($customer)
    {
        $password = str_random(10);

        return [
            "email" => $this->faker->unique()->safeEmail,
            "user_role_id" => UserRole::ROLE_MEMBER,
            "password" => $password,
            "password_confirmation" => $password,
            "customer_id" => $customer->id,
            "contact_information" =>
            [
                'name' => $this->faker->name,
                'address' => $this->faker->address,
                'zip' => $this->faker->postcode,
                'city' => $this->faker->city,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $this->faker->phoneNumber,
                'country_id' => 1
            ]
        ];
    }

    private function regenerateUniqueEmail($data)
    {
        foreach ($data as $key => $value) {
            $data[$key]['email'] = $this->faker->unique()->safeEmail;
        }

        return $data;
    }
}

