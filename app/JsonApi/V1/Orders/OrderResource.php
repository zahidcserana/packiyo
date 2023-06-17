<?php

namespace App\JsonApi\V1\Orders;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class OrderResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        if ($request->page) {
            $productIds = $this->orderItems->pluck('product_id')->toArray();
            $productImages = Image::whereIn('object_id', $productIds)->groupBy('object_id')->pluck('source')->toArray();

            $data = [
                'id' => $this->id,
                'external_id' => $this->external_id,
                'status' => strtolower($this->getStatusText()),
                'ordered_at' => $this->ordered_at,
                'number' => $this->number,
                'order_status_id' => $this->order_status_id,
                'product_images' => $productImages,
                'quantity' => $this->orderItems->sum('quantity'),
                'quantity_allocated_pickable_sum' => $this->quantity_allocated_pickable_sum,
                'has_batch_item' => $this->orderItems->loadCount('pickingBatchItems')->sum('picking_batch_items_count'),
            ];
        } else {
            $this->orderItems = $this->orderItems->load([
                'product' => function($query) {
                    return $query->with([
                        'productImages' => function (MorphMany $query) {
                            return $query->limit(3);
                        }
                    ]);
                }
            ])->map(function ($orderItem) {
                $orderItemData = collect($orderItem)->only(['id', 'quantity', 'product', 'sku', 'external_id'])->toArray();

                $orderItemData['product']['locations'] = [];

                return $orderItemData;
            });

            $data = [
                'id' => $this->id,
                'external_id' => $this->external_id,
                'status' => strtolower($this->getStatusText()),
                'ordered_at' => $this->ordered_at,
                'number' => $this->number,
                'order_items' => $this->orderItems,
                'shipping_contact_information' => $this->shippingContactInformation,
            ];
        }

        return $data;
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('customer')->showDataIfLoaded()->withoutLinks(),
            $this->relation('shipments')->showDataIfLoaded()->withoutLinks(),
            $this->relation('order_items_with_info','orderItemsWithInfo')->showDataIfLoaded()->withoutLinks(),
            $this->relation('shipping_contact_information','shippingContactInformation')->showDataIfLoaded()->withoutLinks(),
            $this->relation('billing_contact_information','billingContactInformation')->showDataIfLoaded()->withoutLinks(),
            $this->relation('order_lock_information','orderLock')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
