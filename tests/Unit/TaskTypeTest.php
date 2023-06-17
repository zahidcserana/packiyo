<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\TaskTypeResource;
use App\Models\TaskType;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class TaskTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $taskType = factory(TaskType::class)->create(['customer_id' => $customer->id]);

        $taskTypeResource = (new TaskTypeResource($taskType))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.task_type.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($taskTypeResource, $res));
        }
    }

    public function testStore()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $taskType = factory(TaskType::class)->create(['customer_id' => $customer->id]);

        $taskTypeResource = (new TaskTypeResource($taskType))->resolve();

        $data = [
            ['name' => $this->faker->word, 'customer_id' => $customer->id]
        ];

        $user = $this->createCustomerUser($customer);

        $this->actingAs($user, 'api')->json('POST', route('api.task_type.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.task_type.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.task_type.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($taskTypeResource, $res));

            $taskType = TaskType::where('id', $res['id'])->first();

            $this->assertTrue($taskType->customer->users->contains('id', $user->id));

            $this->assertFalse($taskType->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $taskType = factory(TaskType::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('view', $taskType));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $taskType));

        $taskTypeResource = (new TaskTypeResource($taskType))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.task_type.show', $taskType->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($taskTypeResource, $response->json()['data']));
    }

    public function testUpdate()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $taskType = factory(TaskType::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('update', $taskType));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $taskType));

        $taskTypeResource = (new TaskTypeResource($taskType))->resolve();

        $data = [
            [
                'id' => $taskType->id,
                'customer_id' => $customer->id,
                'name' => $this->faker->word,
            ],
            [
                'id' => $taskType->id,
                'customer_id' => $customer->id,
                'name' => $this->faker->word,
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.task_type.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($taskTypeResource, $res));

            $taskType = TaskType::where('id', $res['id'])->first();

            $this->assertTrue($taskType->customer->users->contains('id', $user->id));

            $this->assertFalse($taskType->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
    {
        $this->createUserRoles();

        $this->createCustomerUserRoles();

        $customer = $this->createCustomer();

        $taskType = factory(TaskType::class)->create(['customer_id' => $customer->id]);

        $user = $this->createCustomerUser($customer);

        $this->assertTrue($user->can('delete', $taskType));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $taskType));

        $data = [ ['id' => $taskType->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.task_type.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('task_types', ['id' => $value['id']]);
        }
    }
}
