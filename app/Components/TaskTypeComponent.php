<?php

namespace App\Components;

use App\Http\Requests\TaskType\DestroyBatchRequest;
use App\Http\Requests\TaskType\DestroyRequest;
use App\Http\Requests\TaskType\StoreBatchRequest;
use App\Http\Requests\TaskType\StoreRequest;
use App\Http\Requests\TaskType\UpdateBatchRequest;
use App\Http\Requests\TaskType\UpdateRequest;
use App\Http\Resources\TaskTypeCollection;
use App\Http\Resources\TaskTypeResource;
use App\Models\Customer;
use App\Models\TaskType;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;


class TaskTypeComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $taskType = TaskType::create($input);

        if ($fireWebhook) {
            $this->webhook(new TaskTypeResource($taskType), TaskType::class, Webhook::OPERATION_TYPE_STORE, $taskType->customer_id);
        }

        return $taskType;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, TaskType::class, TaskTypeCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, TaskType $taskType, $fireWebhook = true)
    {
        $input = $request->validated();

        $taskType->update($input);

        if ($fireWebhook) {
            $this->webhook(new TaskTypeResource($taskType), TaskType::class, Webhook::OPERATION_TYPE_UPDATE, $taskType->customer_id);
        }

        return $taskType;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $taskType = TaskType::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $taskType, false));
        }

        $this->batchWebhook($responseCollection, TaskType::class, TaskTypeCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, TaskType $taskType, $fireWebhook = true)
    {
        $taskType->delete();

        $response = ['id' => $taskType->id, 'customer_id' => $taskType->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, TaskType::class, Webhook::OPERATION_TYPE_DESTROY, $taskType->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $taskType = TaskType::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $taskType, false));
        }

        $this->batchWebhook($responseCollection, TaskType::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
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
