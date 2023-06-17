<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use DB;
use Tests\Unit\Traits\UnitTestSetup;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker, UnitTestSetup;

    public function testIndex()
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

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.task.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => []
        ]);

        foreach ($response->json()['data'] as $res) {
            $this->assertEmpty(array_diff_key($taskResource, $res));
        }
    }

    public function testStore()
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

        $data = [
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'task_type_id' => $taskType->id,
                'notes' => $this->faker->text
            ],
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'task_type_id' => $taskType->id,
                'notes' => $this->faker->text
            ]
        ];

        $this->actingAs($user, 'api')->json('POST', route('api.task.store'), $data)->assertStatus(200);

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->actingAs($guestUser, 'api')->json('POST', route('api.task.store'), $data)->assertStatus(403);

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('POST', route('api.task.store'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($taskResource, $res));

            $task = Task::where('id', $res['id'])->first();

            $this->assertTrue($task->taskType->customer->users->contains('id', $user->id));

            $this->assertFalse($task->taskType->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testShow()
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

        $this->assertTrue($user->can('view', $task));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('view', $task));

        $taskResource = (new TaskResource($task))->resolve();

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('GET', route('api.task.show', $task->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $this->assertEmpty(array_diff_key($taskResource, $response->json()['data']));
    }

    public function testUpdate()
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

        $this->assertTrue($user->can('update', $task));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('update', $task));

        $taskResource = (new TaskResource($task))->resolve();

        $data = [
            [
                'id' => $task->id,
                'notes' => $this->faker->text,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'task_type_id' => $taskType->id
            ]
        ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('PUT', route('api.task.update'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $res) {
            $this->assertEmpty(array_diff_key($taskResource, $res));

            $task = Task::where('id', $res['id'])->first();

            $this->assertTrue($task->taskType->customer->users->contains('id', $user->id));

            $this->assertFalse($task->taskType->customer->users->contains('id', $guestUser->id));
        }
    }

    public function testDestroy()
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

        $this->assertTrue($user->can('delete', $task));

        $guestCustomer = $this->createCustomer();

        $guestUser = $this->createCustomerUser($guestCustomer);

        $this->assertFalse($guestUser->can('delete', $task));

        $data = [ ['id' => $task->id] ];

        $adminUser = $this->createAdministrator();

        $response = $this->actingAs($adminUser, 'api')->json('DELETE', route('api.task.destroy'), $data);

        $response->assertStatus(200);

        foreach ($response->json() as $key => $value) {
            $this->assertSoftDeleted('tasks', ['id' => $value['id']]);
        }
    }
}
