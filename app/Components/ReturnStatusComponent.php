<?php

namespace App\Components;


use App\Http\Requests\ReturnStatus\DestroyBatchRequest;
use App\Http\Requests\ReturnStatus\DestroyRequest;
use App\Http\Requests\ReturnStatus\StoreBatchRequest;
use App\Http\Requests\ReturnStatus\StoreRequest;
use App\Http\Requests\ReturnStatus\UpdateBatchRequest;
use App\Http\Requests\ReturnStatus\UpdateRequest;
use App\Http\Resources\ReturnStatusCollection;
use App\Http\Resources\ReturnStatusResource;
use App\Models\ReturnStatus;
use App\Models\Webhook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ReturnStatusComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $returnStatus = ReturnStatus::create($input);

        if ($fireWebhook == true) {
            $this->webhook(
                new ReturnStatusResource($returnStatus),
                ReturnStatus::class,
                Webhook::OPERATION_TYPE_STORE,
                $returnStatus->customer_id
            );
        }

        return $returnStatus;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, ReturnStatus::class, ReturnStatusCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, ReturnStatus $returnStatus, $fireWebhook = true)
    {
        $input = $request->validated();

        $returnStatus->update($input);

        if ($fireWebhook == true) {
            $this->webhook(new ReturnStatusResource($returnStatus), ReturnStatus::class, Webhook::OPERATION_TYPE_UPDATE, $returnStatus->customer_id);
        }

        return $returnStatus;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $returnStatus = ReturnStatus::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $returnStatus, false));
        }

        $this->batchWebhook($responseCollection, ReturnStatus::class, ReturnStatusCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, ReturnStatus $returnStatus, $fireWebhook = true)
    {
        $returnStatus->delete();

        $response = ['id' => $returnStatus->id, 'customer_id' => $returnStatus->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, ReturnStatus::class, Webhook::OPERATION_TYPE_DESTROY, $returnStatus->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $returnStatus = ReturnStatus::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $returnStatus, false));
        }

        $this->batchWebhook($responseCollection, ReturnStatus::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }
}
