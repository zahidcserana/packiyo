<?php

namespace App\Components;

use App\Http\Requests\LocationType\{DestroyBatchRequest,
    DestroyRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateRequest};
use App\Http\Requests\Task\UpdateBatchRequest;
use App\Http\Resources\LocationTypeCollection;
use App\Http\Resources\LocationTypeResource;
use App\Models\{Customer, Location, LocationType, Webhook};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class LocationTypeComponent extends BaseComponent
{
    /**
     * @param StoreRequest $request
     * @param bool $fireWebhook
     * @return LocationType|Model
     */
    public function store(StoreRequest $request, bool $fireWebhook = true)
    {
        $input = $request->validated();

        if ($input['sellable'] == 2) {
            $input['sellable'] = null;
        }

        if ($input['pickable'] == 2) {
            $input['pickable'] = null;
        }

        if ($input['disabled_on_picking_app'] == 2) {
            $input['disabled_on_picking_app'] = null;
        }

        $locationType = LocationType::create($input);

        if ($fireWebhook) {
            $this->webhook(new LocationTypeResource($locationType), LocationType::class, Webhook::OPERATION_TYPE_STORE, $locationType->customer_id);
        }

        return $locationType;
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

        $this->batchWebhook($responseCollection, LocationType::class, LocationTypeCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    /**
     * @param UpdateRequest $request
     * @param LocationType $locationType
     * @param bool $fireWebhook
     * @return LocationType
     */
    public function update(UpdateRequest $request, LocationType $locationType, bool $fireWebhook = true): LocationType
    {
        $input = $request->validated();

        if ($input['sellable'] == LocationType::SELLABLE_NOT_SET) {
            $input['sellable'] = null;
        }

        if ($input['pickable'] == LocationType::PICKABLE_NOT_SET) {
            $input['pickable'] = null;
        }

        if ($input['disabled_on_picking_app'] == LocationType::DISABLED_ON_PICKING_APP_NOT_SET) {
            $input['disabled_on_picking_app'] = null;
        }

        $locationType->update($input);

        if ($fireWebhook) {
            $this->webhook(new LocationTypeResource($locationType), LocationType::class, Webhook::OPERATION_TYPE_UPDATE, $locationType->customer_id);
        }

        return $locationType;
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
            $orderStatus = LocationType::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $orderStatus, false));
        }

        $this->batchWebhook($responseCollection, LocationType::class, LocationTypeCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    /**
     * @param DestroyRequest $request
     * @param LocationType $locationType
     * @param bool $fireWebhook
     * @return array
     */
    public function destroy(DestroyRequest $request, LocationType $locationType, bool $fireWebhook = true): array
    {
        $request->validated();

        $locations = Location::whereLocationTypeId($locationType->id)->get();

        foreach ($locations as $location) {
            $location->location_type_id = null;
            $location->saveQuietly();
        }

        $locationType->delete();

        $response = ['id' => $locationType->id, 'customer_id' => $locationType->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, LocationType::class, Webhook::OPERATION_TYPE_DESTROY, $locationType->customer_id);
        }

        return $response;
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
            $locationType = LocationType::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $locationType, false));
        }

        $this->batchWebhook($responseCollection, LocationType::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    /**
     * @param Request $request
     * @param Customer|null $customer
     * @return JsonResponse
     */
    public function getTypes(Request $request, Customer $customer = null): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if (is_null($customer)) {
            return response()->json([
                'results' => $results
            ]);
        }

        if ($term) {
            $term = $term . '%';

            $locationTypes = $customer->locationTypes()->where('name', 'like', $term)->get();

        } else {
            $locationTypes = $customer->locationTypes()->get();
        }

        foreach ($locationTypes as $locationType) {
            if ($locationType->count()) {
                $results[] = [
                    'id' => $locationType->id,
                    'text' => $locationType->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }
}
