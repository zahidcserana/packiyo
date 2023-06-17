<?php

namespace App\Http\Requests\Packing;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\Shipment\ShipItemRequest;
use App\Models\OrderItem;
use App\Models\Printer;
use App\Rules\BelongsToCustomer;
use App\Rules\ExistsOrStaticValue;
use App\Rules\HasDropPoint;
use Illuminate\Support\Arr;

class BulkShipStoreRequest extends FormRequest
{
    public function rules(): array
    {
        $data = collect($this->all())->first();

        $customerId = $data['customer_id'] ?? null;
        $dropPoint = $data['drop_point_id'] ?? null;

        return array_merge(
            [
                '*.shipping_method_id' => [
                    'required',
                    new ExistsOrStaticValue('shipping_methods', 'id', 'dummy'),
                    new HasDropPoint($dropPoint)
                ],
                '*.packing_state' => [
                    'required',
                    'string'
                ],
                '*.printer_id' => [
                    'nullable',
                    'exists:printers,id,deleted_at,NULL',
                    new BelongsToCustomer(Printer::class, $customerId)
                ],
                '*.drop_point_id' => [
                    'nullable'
                ]
            ],
            ShipItemRequest::prefixedValidationRules('*.order_items.*.'),
            ContactInformationStoreRequest::prefixedValidationRules('*.shipping_contact_information.')
        );
    }

    protected function prepareForValidation(): void
    {
        $bulkShipBatch = $this->bulkShipBatch;
        $requestParameters = $this->all();

        $this->removeAllRequestParameters();

        [ $locationsMapping, $ordersMapping ] = $this->getOrderProductLocationMappings($bulkShipBatch, $requestParameters);

        $requestParameters['order_items'] = array_map(static function($requestOrderItem) use ($locationsMapping) {
            return array_merge(
                $requestOrderItem, [
                    'product_id' => $locationsMapping[$requestOrderItem['order_item_id']]['product_id']
                ]
            );
        }, Arr::get($requestParameters, 'order_items'));

        $requests = [];
        foreach ($bulkShipBatch->orders->pluck('id')->toArray() as $orderId) {
            $orderParameters = $this->generateOrderSpecificParameters(
                $orderId,
                $requestParameters,
                $ordersMapping,
                $locationsMapping,
            );

            if (! $orderParameters) {
                continue;
            }

            $requests[$orderId] = [
                '_token' => Arr::get($requestParameters, '_token'),
                'packing_state' => Arr::get($orderParameters, 'packing_state'),
                'customer_id' => Arr::get($requestParameters, 'customer_id'),
                'shipping_method_id' => Arr::get($requestParameters, "shipping_method_id.{$orderId}"),
                'drop_point_id' => Arr::get($requestParameters, 'drop_point_id'),
                'length' => Arr::get($requestParameters, 'length'),
                'width' => Arr::get($requestParameters, 'width'),
                'height' => Arr::get($requestParameters, 'height'),
                'shipping_box' => Arr::get($requestParameters, 'shipping_box'),
                'weight' => Arr::get($requestParameters, 'weight'),
                'order_items' => Arr::get($orderParameters, 'order_items'),
            ];
        }

        $this->request->add($requests);
    }

    private function generateOrderSpecificParameters(
        int $orderId,
        array $requestParameters,
        array &$ordersMapping,
        array &$locationsMapping
    ): ?array {
        $orderItems = [];

        $orderItemsWithoutLocations = $this->removeLocationMappingFromRequestOrderItems($requestParameters['order_items']);

        foreach ($orderItemsWithoutLocations as $item) {
            $orderMappingIndexInUse = 0;

            foreach ($ordersMapping as $index => $orderMapping) {
                if ($orderMapping['order_id'] == $orderId &&
                    $orderMapping['product_id'] == $item['product_id'] &&
                    Arr::get($ordersMapping, $index . '.quantity_used', 0) == 0
                ) {
                    $orderMappingIndexInUse = $index;
                    break;
                }
            }

            $orderItemId = data_get($ordersMapping, "{$orderMappingIndexInUse}.id");

            $locations = [];
            $quantityToShip = (int) $item['quantity'];
            $quantityUsed = 0;
            foreach ($locationsMapping[$item['order_item_id']]['locations'] as $locationId => $quantityOnHand) {
                if ($quantityOnHand === 0) {
                    return null;
                }

                if ($quantityOnHand >= $quantityToShip) {
                    $locations[$locationId] = $quantityToShip;
                    $locationsMapping[$item['order_item_id']]['locations'][$locationId] = $quantityOnHand - $quantityToShip;
                    break;
                }

                $locations[$locationId] = $quantityOnHand;
                $locationsMapping[$item['order_item_id']]['locations'][$locationId] = 0;
                $quantityToShip -= $quantityOnHand;
            }

            foreach ($locations as $locationId => $quantity) {
                $newKey = [
                    $item['index'],
                    $orderItemId,
                    $locationId,
                    '',
                    $item['package'],
                ];

                $orderItems[implode('_', $newKey)] = [
                    'quantity' => $quantity,
                    'order_item_id' => $orderItemId,
                    'location_id' => $locationId,
                    'tote_id' => null,
                    'product_id' => $item['product_id'],
                ];

                $quantityUsed += $quantity;
            }

            Arr::set($ordersMapping, $orderMappingIndexInUse . '.quantity_used', $quantityUsed);
        }

        return [
            'order_items' => $orderItems,
            'packing_state' => $this->generatePackingState(
                $orderItems,
                json_decode($requestParameters['packing_state'], true),
            ),
        ];
    }

    public function removeAllRequestParameters(): static
    {
        $keys = array_keys($this->all());

        foreach ($keys as $key) {
            $this->request->remove($key);
        }

        return $this;
    }

    private function getOrderProductLocationMappings(mixed $bulkShipBatch, array $requestParameters): array
    {
        $locationsMapping = OrderItem::whereIn('id', collect($requestParameters['order_items'])->pluck('order_item_id')->toArray())
            ->with([
                'product.locations' => function($query) {
                    return $query->select('location_id', 'quantity_on_hand');
                }
            ])
            ->get()
            ->map(function($orderItem) use ($requestParameters) {
                $requestOrderItem = collect(array_values($requestParameters['order_items']))
                    ->where('order_item_id', $orderItem->id)
                    ->first();

                return [
                    'id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'locations' => $orderItem->product
                        ->locations
                        ->where('location_id', Arr::get($requestOrderItem, 'location_id'))
                        ->pluck('quantity_on_hand', 'location_id')
                        ->toArray(),
                    'quantity' => Arr::get($requestOrderItem, 'quantity'),
                ];
            })
            ->keyBy('id')
            ->toArray();

        foreach ($locationsMapping as $orderItemId => $product) {
            foreach (Arr::get($product, 'locations') as $locationId => $quantityOnHand) {
                Arr::set(
                    $locationsMapping,
                    $orderItemId . '.locations.' . $locationId,
                    $quantityOnHand,
                );
            }
        }

        $ordersMapping = OrderItem::whereIn('order_id', $bulkShipBatch->orders->pluck('id')->toArray())
            ->where('quantity_allocated', '>', 0)
            ->select(['id', 'order_id', 'product_id', 'quantity_allocated'])
            ->get()
            ->toArray();

        return [$locationsMapping, $ordersMapping];
    }

    private function removeLocationMappingFromRequestOrderItems(array $order_items): array
    {
        $items = [];

        collect($order_items)->map(function($item, $key) use (&$items) {
            $keyArray = explode('_', $key);
            $orderItemId = $item['order_item_id'];
            $newKey = "{$orderItemId}_{$keyArray[4]}";

            if (! isset($items[$newKey])) {
                $items[$newKey] = [
                    'order_item_id' => $orderItemId,
                    'quantity' => $item['quantity'],
                    'product_id' => $item['product_id'],
                    'index' => $keyArray[0],
                    'package' => $keyArray[4],
                ];
            } else {
                $items[$newKey]['quantity'] += $item['quantity'];
            }

            $items[$newKey]['keys'][] = $key;

            return [];
        });

        return $items;
    }

    private function generatePackingState($orderItems, array $packingState): string
    {
        $orderItems = collect($orderItems);

        foreach ($packingState as $packageIndex => $package) {
            $quantity = count($package['items']);
            $packingState[$packageIndex]['items'] = [];

            $orderItemsPackage = $orderItems->shift();
            for ($i = 0; $i < $quantity; $i++) {
                if ($orderItemsPackage['quantity'] === 0) {
                    $orderItemsPackage = $orderItems->shift();
                }

                $packingState[$packageIndex]['items'][] = [
                    'orderItem' => (string) $orderItemsPackage['order_item_id'],
                    'location' => (string) $orderItemsPackage['location_id'],
                    'tote' => '',
                    'product_id' => $orderItemsPackage['product_id'],
                ];
                $orderItemsPackage['quantity']--;
            }
        }


        return json_encode($packingState, JSON_THROW_ON_ERROR);
    }

    public function getOrderRequestInstance(int $orderId): ?static
    {
        $request = clone $this;

        $request->removeAllRequestParameters();

        if (! Arr::get($this, $orderId)) {
            return null;
        }

        $request->request->add($this[$orderId]);

        return $request;
    }
}
