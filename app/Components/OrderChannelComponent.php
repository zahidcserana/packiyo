<?php

namespace App\Components;

use App\Http\Requests\OrderChannel\DestroyBatchRequest;
use App\Http\Requests\OrderChannel\DestroyRequest;
use App\Http\Requests\OrderChannel\StoreBatchRequest;
use App\Http\Requests\OrderChannel\StoreRequest;
use App\Http\Requests\OrderChannel\UpdateBatchRequest;
use App\Http\Requests\OrderChannel\UpdateRequest;
use App\Http\Resources\OrderChannelCollection;
use App\Http\Resources\OrderChannelResource;
use App\Models\OrderChannel;
use App\Models\Webhook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class OrderChannelComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $orderChannel = OrderChannel::create($input);

        if ($fireWebhook == true) {
            $this->webhook(new OrderChannelResource
                ($orderChannel), OrderChannel::class, Webhook::OPERATION_TYPE_STORE, $orderChannel->customer_id);
        }

        return $orderChannel;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, OrderChannel::class, OrderChannelCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, OrderChannel $orderChannel, $fireWebhook = true)
    {
        $input = $request->validated();

        $orderChannel->update($input);

        if ($fireWebhook == true) {
            $this->webhook(new OrderChannelResource($orderChannel), OrderChannel::class, Webhook::OPERATION_TYPE_UPDATE, $orderChannel->customer_id);
        }

        return $orderChannel;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $orderChannel = OrderChannel::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $orderChannel, false));
        }

        $this->batchWebhook($responseCollection, OrderChannel::class, OrderChannelCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, OrderChannel $orderChannel, $fireWebhook = true)
    {
        $orderChannel->delete();

        $response = ['id' => $orderChannel->id, 'customer_id' => $orderChannel->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, OrderChannel::class, Webhook::OPERATION_TYPE_DESTROY, $orderChannel->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $orderChannel = OrderChannel::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $orderChannel, false));
        }

        $this->batchWebhook($responseCollection, OrderChannel::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }
}
