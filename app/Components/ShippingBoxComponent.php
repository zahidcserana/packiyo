<?php

namespace App\Components;

use App\Http\Requests\ShippingBox\DestroyBatchRequest;
use App\Http\Requests\ShippingBox\DestroyRequest;
use App\Http\Requests\ShippingBox\StoreBatchRequest;
use App\Http\Requests\ShippingBox\StoreRequest;
use App\Http\Requests\ShippingBox\UpdateBatchRequest;
use App\Http\Requests\ShippingBox\UpdateRequest;
use App\Http\Resources\ShippingBoxCollection;
use App\Http\Resources\ShippingBoxResource;
use App\Models\ShippingBox;
use App\Models\Webhook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ShippingBoxComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $shippingBox = ShippingBox::create($input);

        if ($fireWebhook == true) {
            $this->webhook(new ShippingBoxResource
            ($shippingBox), ShippingBox::class, Webhook::OPERATION_TYPE_STORE, $shippingBox->customer_id);
        }

        return $shippingBox;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, ShippingBox::class, ShippingBoxCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, ShippingBox $shippingBox, $fireWebhook = true)
    {
        $input = $request->validated();

        $shippingBox->update($input);

        if ($fireWebhook == true) {
            $this->webhook(new ShippingBoxResource($shippingBox), ShippingBox::class, Webhook::OPERATION_TYPE_UPDATE, $shippingBox->customer_id);
        }

        return $shippingBox;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $shippingBox = ShippingBox::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $shippingBox, false));
        }

        $this->batchWebhook($responseCollection, ShippingBox::class, ShippingBoxCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, ShippingBox $shippingBox, $fireWebhook = true)
    {
        $shippingBox->delete();

        $response = ['id' => $shippingBox->id, 'customer_id' => $shippingBox->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, ShippingBox::class, Webhook::OPERATION_TYPE_DESTROY, $shippingBox->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $shippingBox = ShippingBox::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $shippingBox, false));
        }

        $this->batchWebhook($responseCollection, ShippingBox::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }
}
