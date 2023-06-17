<?php

namespace App\Components;

use App\Http\Requests\ShippingMethodMapping\DestroyBatchRequest;
use App\Http\Requests\ShippingMethodMapping\DestroyRequest;
use App\Http\Requests\ShippingMethodMapping\StoreBatchRequest;
use App\Http\Requests\ShippingMethodMapping\StoreRequest;
use App\Http\Requests\ShippingMethodMapping\UpdateBatchRequest;
use App\Http\Requests\ShippingMethodMapping\UpdateRequest;
use App\Http\Resources\ShippingMethodMappingCollection;
use App\Http\Resources\ShippingMethodMappingResource;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\ShippingMethodMapping;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class ShippingMethodMappingComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $shippingMethodMapping = ShippingMethodMapping::create($input);

        if ($fireWebhook) {
            $this->webhook(new ShippingMethodMappingResource($shippingMethodMapping), ShippingMethodMapping::class, Webhook::OPERATION_TYPE_STORE, $shippingMethodMapping->customer_id);
        }

        return $shippingMethodMapping;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, ShippingMethodMapping::class, ShippingMethodMappingCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, ShippingMethodMapping $shippingMethodMapping, $fireWebhook = true)
    {
        $input = $request->validated();

        $shippingMethodMapping->update($input);

        if ($fireWebhook) {
            $this->webhook(new ShippingMethodMappingResource($shippingMethodMapping), ShippingMethodMapping::class, Webhook::OPERATION_TYPE_UPDATE, $shippingMethodMapping->customer_id);
        }

        return $shippingMethodMapping;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $shippingMethodMapping = ShippingMethodMapping::where('number', $record['number'])->first();

            $responseCollection->add($this->update($updateRequest, $shippingMethodMapping, false));
        }

        $this->batchWebhook($responseCollection, ShippingMethodMapping::class, ShippingMethodMappingCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request, ShippingMethodMapping $shippingMethodMapping, $fireWebhook = true)
    {
        $shippingMethodMapping->delete();

        $response = ['id' => $shippingMethodMapping->id, 'customer_id' => $shippingMethodMapping->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, ShippingMethodMapping::class, Webhook::OPERATION_TYPE_DESTROY, $shippingMethodMapping->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $shippingMethodMapping = ShippingMethodMapping::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $shippingMethodMapping, false));
        }

        $this->batchWebhook($responseCollection, ShippingMethodMapping::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

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
                })
                ->get();

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

    public function filterShippingMethods(Customer $customer = null, $term = ''): Collection
    {
        if (is_null($customer)) {
            return collect([]);
        }

        $customers[] = $customer->id;

        if ($customer->parent) {
            $customers[] = $customer->parent_id;
        }

        $shippingMethodQuery = ShippingMethod::whereHas('shippingCarrier', static function ($query) use ($customers) {
                $query->whereIn('customer_id', $customers);
            });

        if ($term) {
            $term .= '%';

            $shippingMethodQuery = $shippingMethodQuery->where(static function ($q) use ($term) {
                $q->whereHas('shippingCarrier', static function($query) use ($term) {
                    $query->where('name', 'like', $term);
                })
                ->orWhere('name', 'like', $term);
            });
        }

        return $shippingMethodQuery->with('shippingCarrier')->get();
    }

    public function setShippingMethodToOrders(ShippingMethodMapping $shippingMethodMapping) {
        $orders = Order::where('customer_id', $shippingMethodMapping->customer_id)
            ->whereNull('shipping_method_id')
            ->whereNull('fulfilled_at')
            ->whereNull('cancelled_at')
            ->where('shipping_method_name', $shippingMethodMapping->shipping_method_name)
            ->get();

        foreach ($orders as $order) {
            $this->getShippingMethod($order);
        }
    }

    public function getShippingMethod(Order $order) {
        $customer = $order->customer;
        $shippingMethodName = $order->shipping_method_name;
        $shippingMethodCode = $order->shipping_method_code;

        $dropPointId = null;
        $shippingMethod = null;

        $shippingMethodMapping = ShippingMethodMapping::where('customer_id', $customer->id)
            ->where(function (Builder $query) use ($shippingMethodName, $shippingMethodCode) {
                return $query->whereRaw("? LIKE `shipping_method_name`", ['shipping_method_name' => $shippingMethodName])
                    ->orWhereRaw("? LIKE `shipping_method_name`", ['shipping_method_code' => $shippingMethodCode]);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($shippingMethodMapping) {
            $shippingMethod = $shippingMethodMapping->shippingMethod;

            $returnShippingMethod = $shippingMethodMapping->returnShippingMethod;
        }

        if (!$shippingMethod && $shippingMethodCode) {
            $decodedMethod = json_decode($shippingMethodCode, true);

            if (is_array($decodedMethod)) {
                $shippingRateId = Arr::get($decodedMethod, 'shipping_rate_id');

                if (!$shippingRateId) {
                    foreach ($decodedMethod as $decodedMethodData) {
                        if (Arr::get($decodedMethodData, 'key') === 'shipping_rate_id') {
                            $shippingRateId = (int)Arr::get($decodedMethodData, 'value');
                            break;
                        }
                    }
                }

                if ($shippingRateId) {
                    $shippingMethod = ShippingMethod::with('shippingCarrier')
                        ->select('shipping_methods.*')
                        ->leftJoin('shipping_carriers', 'shipping_carriers.id', '=', 'shipping_methods.shipping_carrier_id')
                        ->where('shipping_carriers.customer_id', $customer->id)
                        ->whereJsonContains('shipping_methods.settings', ['external_method_id' => $shippingRateId])
                        ->orderBy('shipping_methods.created_at', 'desc')
                        ->first();

                    if ($shippingMethod) {
                        $dropPointId = Arr::get($decodedMethod, 'drop_point.drop_point_id');
                    }
                }
            }
        }

        if (!$shippingMethod) {
            $shippingMethod = ShippingMethod::with('shippingCarrier')
                ->select('shipping_methods.*')
                ->leftJoin('shipping_carriers', 'shipping_carriers.id', '=', 'shipping_methods.shipping_carrier_id')
                ->where('shipping_carriers.customer_id', $customer->id)
                ->where('shipping_methods.name', $shippingMethodName)
                ->orderBy('shipping_methods.created_at', 'desc')
                ->first();
        }

        if ($shippingMethod) {
            $order->shipping_method_id = $shippingMethod->id;

            if (isset($returnShippingMethod)) {
                $order->return_shipping_method_id = $returnShippingMethod->id;
            }

            if ($dropPointId) {
                $order->drop_point_id = $dropPointId;
            }

            $order->saveQuietly();

            return $shippingMethod;
        }

        return false;
    }

    /**
     * @param Order $order
     * @return array|mixed|null
     */
    public function returnShippingMethod(Order $order)
    {
        $customerId = $order->customer_id;
        $shippingMethodName = $order->shipping_method_name;
        $shippingMethodCode = $order->shipping_method_code;

        $shippingMethodMapping = ShippingMethodMapping::where('customer_id', $customerId)
            ->where(function (Builder $query) use ($shippingMethodName, $shippingMethodCode) {
                return $query->whereRaw("? LIKE `shipping_method_name`", ['shipping_method_name' => $shippingMethodName])
                    ->orWhereRaw("? LIKE `shipping_method_name`", ['shipping_method_code' => $shippingMethodCode]);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($shippingMethodMapping) {
            $shippingMethod = $shippingMethodMapping->returnShippingMethod;
        } else {
            $shippingMethodId = customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_DEFAULT_RETURN_SHIPPING_METHOD, $order->shipping_method_id);

            $shippingMethod = ShippingMethod::find($shippingMethodId);
        }

        return $shippingMethod;
    }
}
