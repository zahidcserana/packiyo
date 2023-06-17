<?php

namespace App\Components;

use App\Http\Requests\WebshipperCredential\{DestroyBatchRequest,
    DestroyRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateBatchRequest,
    UpdateRequest};
use App\Models\{Customer, WebshipperCredential};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class WebshipperCredentialComponent extends BaseComponent
{
    /**
     * @param StoreRequest $request
     * @param Customer|null $customer
     * @return WebshipperCredential|Model
     */
    public function store(StoreRequest $request, Customer $customer = null): Model|WebshipperCredential
    {
        $input = $request->validated();

        if (!is_null($customer)) {
            $input['customer_id'] = $customer->id;
        }

        return WebshipperCredential::create($input);
    }

    /**
     * @param StoreBatchRequest $request
     * @return Collection
     */
    public function storeBatch(StoreBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest));
        }

        return $responseCollection;
    }

    /**
     * @param UpdateRequest $request
     * @param WebshipperCredential $webshipperCredential
     * @return WebshipperCredential
     */
    public function update(UpdateRequest $request, WebshipperCredential $webshipperCredential): WebshipperCredential
    {
        $input = $request->validated();

        $webshipperCredential->update($input);

        return $webshipperCredential;
    }

    /**
     * @param UpdateBatchRequest $request
     * @return Collection
     */
    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $webshipperCredential = WebshipperCredential::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $webshipperCredential));
        }

        return $responseCollection;
    }

    /**
     * @param DestroyRequest $request
     * @param WebshipperCredential $webshipperCredential
     * @return array
     */
    public function destroy(DestroyRequest $request, WebshipperCredential $webshipperCredential): array
    {
        $webshipperCredential->delete();

        return ['id' => $webshipperCredential->id, 'customer_id' => $webshipperCredential->customer_id];
    }

    /**
     * @param DestroyBatchRequest $request
     * @return Collection
     */
    public function destroyBatch(DestroyBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $webshipperCredential = WebshipperCredential::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $webshipperCredential));
        }

        return $responseCollection;
    }
}
