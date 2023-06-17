<?php

namespace App\Http\Resources;

use App\Models\Shipment;
use App\Models\ShipmentLabel;
use App\Models\ShipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentReportTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($resource);
        $trackingNumbers = '';
        $shipmentLabels = '';
        $lineItemTotal = 0;
        $shipmentProducts = [];
        $labelNumber = 1;

        foreach($this->shipmentTrackings as $tracking) {
            if ($tracking->type != ShipmentTracking::TYPE_RETURN) {
                $trackingNumbers .= '<a href="' . $tracking->tracking_url . '" target="_blank" class="text-neutral-text-gray">' . $tracking->tracking_number . '</a><br/>';
            }
        }

        foreach ($this->shipmentLabels as $shipmentLabel) {
            if ($shipmentLabel->type != ShipmentLabel::TYPE_RETURN) {
                $route = route('shipment.label', [
                    'shipment' => $this,
                    'shipmentLabel' => $shipmentLabel,
                ]);

                $label = __('Label :number', ['number' => $labelNumber++]);

                $shipmentLabels .= '<a href="' . $route .'" title="' . $label . '" target="_blank" class="text-neutral-text-gray"><i class="picon-tag-light icon-lg"></i></a><br/> ';
            }
        }

        foreach ($this->shipmentItems as $shipmentItem) {

            $lineItemTotal+= $shipmentItem['quantity'];

            if (isset($shipmentProducts[$shipmentItem->order_item_id])) {
                $shipmentProducts[$shipmentItem->order_item_id]['quantity'] += $shipmentItem['quantity'];
            } else {
                $shipmentProducts[$shipmentItem->order_item_id]['quantity'] = $shipmentItem['quantity'];
                $shipmentProducts[$shipmentItem->order_item_id]['name'] = $shipmentItem->orderItem->name;
                $shipmentProducts[$shipmentItem->order_item_id]['sku'] = $shipmentItem->orderItem->sku;
            }
        }

        $resource['id'] = $this->id;
        $resource['order'] = ['id' => $this->order->id, 'number' => $this->order->number];
        $resource['order_products'] = $shipmentProducts;
        $resource['order_date'] = user_date_time($this->order->ordered_at, true);
        $resource['shipment_date'] = user_date_time($this->created_at, true);
        $resource['tracking_number'] = $trackingNumbers;
        $resource['shipment_labels'] = $shipmentLabels;
        $resource['shipping_carrier'] = $this->shippingMethod->shippingCarrier->name ?? '';
        $resource['shipping_method'] = $this->shippingMethod->name ?? '';
        $resource['order_shipping_name'] = $this->contactInformation->name;
        $resource['order_shipping_address'] = $this->contactInformation->address;
        $resource['order_shipping_address2'] = $this->contactInformation->address2;
        $resource['order_shipping_city'] = $this->contactInformation->city;
        $resource['order_shipping_state'] = $this->contactInformation->state;
        $resource['order_shipping_zip'] = $this->contactInformation->zip;
        $resource['order_shipping_country'] = $this->contactInformation->country->name;
        $resource['order_shipping_company'] = $this->contactInformation->company_name;
        $resource['order_shipping_phone'] = $this->contactInformation->phone;

        $resource['distinct_items'] = count($this->order->orderItems);
        $resource['lines_shipped'] = count($this->shipmentItems);
        $resource['line_item_total'] = $lineItemTotal;

        $resource['order_shipping_email'] = $this->contactInformation->email;

        $resource['user_id'] = $this->user->contactInformation->name ?? '';
        $resource['voided_at'] = $this->voided_at;
        $resource['void_link'] = ['token' => csrf_token(), 'url' => route('shipments.void', ['shipment' => $this]), 'title' => __('Void Label')];

        return $resource;
    }
}
