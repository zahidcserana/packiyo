<?php

namespace App\Components;

use App\Http\Requests\Task\DestroyBatchRequest;
use App\Http\Requests\Task\DestroyRequest;
use App\Http\Requests\Task\StoreBatchRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateBatchRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TaskComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $task = Task::create($input);

        if ($fireWebhook) {
            $this->webhook(new TaskResource($task), Task::class, Webhook::OPERATION_TYPE_STORE, $task->customer_id);
        }

        return $task;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Task::class, TaskCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Task $task, $fireWebhook = true)
    {
        $input = $request->validated();

        $task->update($input);

        if ($fireWebhook) {
            $this->webhook(new TaskResource($task), Task::class, Webhook::OPERATION_TYPE_UPDATE, $task->customer_id);
        }

        return $task;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $task = Task::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $task, false));
        }

        $this->batchWebhook($responseCollection, Task::class, TaskCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, Task $task, $fireWebhook = true)
    {
        $task->delete();

        $response = ['id' => $task->id, 'customer_id' => $task->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, Task::class, Webhook::OPERATION_TYPE_DESTROY, $task->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $task = Task::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $task, false));
        }

        $this->batchWebhook($responseCollection, Task::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function getUserTasks(User $user)
    {
        $userIds = array();

        $customers = Customer::with('users')->whereHas('users', function ($query) use($user){
             $query->where('customer_user.user_id', $user->id);
        })->get();

        $customers->each(function ($item, $key) use(&$userIds){
            $item->users->each(function ($user_item, $key) use(&$userIds){
                $userIds[] = $user_item->id;
            });
        });

        return Task::whereIn('user_id', array_unique($userIds))->paginate();
    }

    public function getCustomerTasks(Customer $customer): LengthAwarePaginator
    {
        return $customer->tasks()->paginate();
    }

    public function filterUsers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = User::whereHas('contactInformation', function ($query) use ($term) {
                    $query->where('name', 'like', $term . '%' )
                        ->orWhere('company_name', 'like',$term . '%')
                        ->orWhere('email', 'like',  $term . '%' )
                        ->orWhere('zip', 'like', $term . '%' )
                        ->orWhere('city', 'like', $term . '%' )
                        ->orWhere('phone', 'like', $term . '%' );
                })->get();

            foreach ($contactInformation as $information) {
                $results[] = [
                    'id' => $information->id,
                    'text' => $information->contactInformation->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', static function($query) use ($term) {
                $query->where('name', 'like', $term . '%' )
                    ->orWhere('company_name', 'like',$term . '%')
                    ->orWhere('email', 'like',  $term . '%' )
                    ->orWhere('zip', 'like', $term . '%' )
                    ->orWhere('city', 'like', $term . '%' )
                    ->orWhere('phone', 'like', $term . '%' );
            })->get();

            foreach ($contactInformation as $information) {
                $results[] = [
                    'id' => $information->id,
                    'text' => $information->contactInformation->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }
}
