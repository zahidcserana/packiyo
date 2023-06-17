<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = parent::toArray($request);

        $resource['shipment_items'] = (new ShipmentItemCollection($this->shipmentItems))->toArray($request);
        $resource['shipment_trackings'] = $this->shipmentTrackings;
        $resource['shipment_labels'] = (new ShipmentLabelCollection($this->shipmentLabels))->toArray($request);
        $resource['order'] = $this->order;

        $resource['contact_information'] = new ContactInformationResource($this->contactInformation);

        if ($this->shippingMethod) {
            $resource['shipping_method'] = new ShippingMethodResource($this->shippingMethod);
        } else {
            $resource['shipping_method'] = null;
        }

        return $resource;
    }
}
