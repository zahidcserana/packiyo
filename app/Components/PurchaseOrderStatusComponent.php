<?php

namespace App\Components;

use App\Http\Requests\PurchaseOrderStatus\DestroyBatchRequest;
use App\Http\Requests\PurchaseOrderStatus\DestroyRequest;
use App\Http\Requests\PurchaseOrderStatus\StoreBatchRequest;
use App\Http\Requests\PurchaseOrderStatus\StoreRequest;
use App\Http\Requests\PurchaseOrderStatus\UpdateBatchRequest;
use App\Http\Requests\PurchaseOrderStatus\UpdateRequest;
use App\Http\Resources\PurchaseOrderStatusCollection;
use App\Http\Resources\PurchaseOrderStatusResource;
use App\Models\PurchaseOrderStatus;
use App\Models\Webhook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class PurchaseOrderStatusComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $purchaseOrderStatus = PurchaseOrderStatus::create($input);

        if ($fireWebhook == true) {
            $this->webhook(new PurchaseOrderStatusResource($purchaseOrderStatus), PurchaseOrderStatus::class, Webhook::OPERATION_TYPE_STORE, $purchaseOrderStatus->customer_id);
        }

        return $purchaseOrderStatus;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, PurchaseOrderStatus::class, PurchaseOrderStatusCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, PurchaseOrderStatus $purchaseOrderStatus, $fireWebhook = true)
    {
        $input = $request->validated();

        $purchaseOrderStatus->update($input);

        if ($fireWebhook == true) {
            $this->webhook(new PurchaseOrderStatusResource($purchaseOrderStatus), PurchaseOrderStatus::class, Webhook::OPERATION_TYPE_UPDATE, $purchaseOrderStatus->customer_id);
        }

        return $purchaseOrderStatus;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $purchaseOrderStatus = PurchaseOrderStatus::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $purchaseOrderStatus, false));
        }

        $this->batchWebhook($responseCollection, PurchaseOrderStatus::class, PurchaseOrderStatusCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, PurchaseOrderStatus $purchaseOrderStatus, $fireWebhook = true)
    {
        $purchaseOrderStatus->delete();

        $response = ['id' => $purchaseOrderStatus->id, 'customer_id' => $purchaseOrderStatus->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, PurchaseOrderStatus::class, Webhook::OPERATION_TYPE_DESTROY, $purchaseOrderStatus->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $purchaseOrderStatus = PurchaseOrderStatus::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $purchaseOrderStatus, false));
        }

        $this->batchWebhook($responseCollection, PurchaseOrderStatus::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }
}
