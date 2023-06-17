<?php

namespace App\Components;

use App\Http\Requests\Supplier\DestroyBatchRequest;
use App\Http\Requests\Supplier\DestroyRequest;
use App\Http\Requests\Supplier\StoreBatchRequest;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateBatchRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Resources\SupplierCollection;
use App\Http\Resources\SupplierResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SupplierComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $contactInformationData = Arr::get($input, 'contact_information');

        Arr::forget($input, 'contact_information');

        $supplier = Supplier::create($input);

        $this->createContactInformation($contactInformationData, $supplier);

        if ($fireWebhook) {
            $this->webhook(new SupplierResource($supplier), Supplier::class, Webhook::OPERATION_TYPE_STORE, $supplier->customer_id);
        }

        return $supplier;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Supplier::class, SupplierCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Supplier $supplier, $fireWebhook = true)
    {
        $input = $request->validated();

        $supplier->contactInformation->update(Arr::get($input, 'contact_information'));

        if (array_key_exists('product_id', $input)) {
            foreach ($input['product_id'] as $productId) {
                $supplier->products()->attach($productId);
            }
        }

        Arr::forget($input, 'contact_information');
        $supplier->update($input);

        if ($fireWebhook) {
            $this->webhook(new SupplierResource($supplier), Supplier::class, Webhook::OPERATION_TYPE_UPDATE, $supplier->customer_id);
        }

        return $supplier;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $supplier = Supplier::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $supplier, false));
        }

        $this->batchWebhook($responseCollection, Supplier::class, SupplierCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, Supplier $supplier, $fireWebhook = true)
    {
        $supplier->delete();

        $response = ['id' => $supplier->id, 'customer_id' => $supplier->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, Supplier::class, Webhook::OPERATION_TYPE_DESTROY, $supplier->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $supplier = Supplier::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $supplier, false));
        }

        $this->batchWebhook($responseCollection, Supplier::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

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

    public function filterProducts(Request $request, Customer $customer): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $term = $term . '%';

            $products = Product::where('customer_id', $customer->id)
                ->where(static function ($query) use ($term) {
                    return $query->where('sku', 'like', $term)
                        ->orWhere('name', 'like', $term);
                })
                ->get();

            foreach ($products as $product) {
                $results[] = [
                    'id' => $product->id,
                    'text' => 'SKU: ' . $product->sku . ', NAME:' . $product->name,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $product->quantity_available ?? 0
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterByProduct(Request $request, Product $product)
    {
        $term = $request->get('term');
        $suppliers = null;

        if ($term) {
            $term = $term . '%';
            $suppliers = Supplier::whereHas('products', function ($query) use($product) {
                $query->where('products.id', $product->id);
            })
            ->whereHas('contactInformation', function ($query) use ($term) {

                $query->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
            ->get();
        }

        return $suppliers;
    }
}
