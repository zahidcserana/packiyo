<?php

namespace App\Components;

use App\Http\Requests\Warehouses\DestroyBatchRequest;
use App\Http\Requests\Warehouses\DestroyRequest;
use App\Http\Requests\Warehouses\StoreBatchRequest;
use App\Http\Requests\Warehouses\StoreRequest;
use App\Http\Requests\Warehouses\UpdateBatchRequest;
use App\Http\Requests\Warehouses\UpdateRequest;
use App\Http\Resources\WarehouseCollection;
use App\Http\Resources\WarehouseResource;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Warehouse;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class WarehouseComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $contactInformationData = Arr::get($input, 'contact_information');

        $warehouse = Warehouse::create([
            'customer_id' => Arr::get($input, 'customer_id')
        ]);

        $this->createContactInformation($contactInformationData, $warehouse);

        if ($fireWebhook) {
            $this->webhook(new WarehouseResource($warehouse), Warehouse::class, Webhook::OPERATION_TYPE_STORE, $warehouse->customer_id);
        }

        return $warehouse;
    }

    public function storeBatch(StoreBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Warehouse::class, WarehouseCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Warehouse $warehouse, $fireWebhook = true): Warehouse
    {
        $input = $request->validated();

        $warehouse->contactInformation->update(Arr::get($input, 'contact_information'));
        $warehouse->update($input);

        if ($fireWebhook) {
            $this->webhook(new WarehouseResource($warehouse), Warehouse::class, Webhook::OPERATION_TYPE_UPDATE, $warehouse->customer_id);
        }

        return $warehouse;
    }

    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $warehouse = Warehouse::where('id', $record['id'])->first();

            $responseCollection->add($this->update($updateRequest, $warehouse, false));
        }

        $this->batchWebhook($responseCollection, Warehouse::class, WarehouseCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request = null, Warehouse $warehouse = null, Customer $customer = null, $fireWebhook = true)
    {
        if (!$customer) {
            $warehouse->delete();
        } else {
            $warehouse->customers()->detach($customer->id);

            if (!$warehouse->customers()->count()) {
                $warehouse->delete();
            }
        }

        $response = ['id' => $warehouse->id, 'customer_id' => $warehouse->customer_id];

        if ($fireWebhook == true) {
            $this->webhook($response, Warehouse::class, Webhook::OPERATION_TYPE_DESTROY, $warehouse->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $warehouse = Warehouse::where('id', $record['id'])->first();
            $customer = null;

            if (!empty($record['customer_id'])) {
                $customer = Customer::find($record['customer_id']);
            }
            $responseCollection->add($this->destroy($destroyRequest, $warehouse, $customer, false));
        }

        $this->batchWebhook($responseCollection, Warehouse::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function addCustomers(Request $request, Warehouse $warehouse)
    {
        return $warehouse->update(['customer_id' => $request->get('customer_id')]);

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

    public function getCustomerWarehouses(Customer $customer)
    {
        return $customer->warehouses()->paginate();
    }
}
