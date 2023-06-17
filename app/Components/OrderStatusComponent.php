<?php

namespace App\Components;

use App\Http\Requests\OrderStatus\DestroyBatchRequest;
use App\Http\Requests\OrderStatus\DestroyRequest;
use App\Http\Requests\OrderStatus\StoreBatchRequest;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateBatchRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Http\Resources\OrderStatusCollection;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use App\Models\Webhook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class OrderStatusComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $orderStatus = OrderStatus::create($input);

        if ($fireWebhook == true) {
            $this->webhook(new OrderStatusResource
                ($orderStatus), OrderStatus::class, Webhook::OPERATION_TYPE_STORE, $orderStatus->customer_id);
        }

        return $orderStatus;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, OrderStatus::class, OrderStatusCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, OrderStatus $orderStatus, $fireWebhook = true)
    {
        $input = $request->validated();

        $orderStatus->update($input);

        if ($fireWebhook == true) {
            $this->webhook(new OrderStatusResource($orderStatus), OrderStatus::class, Webhook::OPERATION_TYPE_UPDATE, $orderStatus->customer_id);
        }

        return $orderStatus;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $orderStatus = OrderStatus::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $orderStatus, false));
        }

        $this->batchWebhook($responseCollection, OrderStatus::class, OrderStatusCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, OrderStatus $orderStatus, $fireWebhook = true)
    {
        $orderStatus->delete();

        $response = ['id' => $orderStatus->id, 'customer_id' => $orderStatus->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, OrderStatus::class, Webhook::OPERATION_TYPE_DESTROY, $orderStatus->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $orderStatus = OrderStatus::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $orderStatus, false));
        }

        $this->batchWebhook($responseCollection, OrderStatus::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }
}
