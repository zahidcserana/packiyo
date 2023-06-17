<?php

namespace App\Http\Resources\ExportResources;

use App\Models\ShippingMethodMapping;
use Illuminate\Http\Request;

class OrderExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $resource = [];

        if (isset($this->orderItems)) {
            foreach ($this->orderItems as $orderItem) {
                $resource[] = [
                    'order_number' => $this->number,
                    'status' => $this->getStatusText(),
                    'sku' => $orderItem->product->sku,
                    'quantity' => $orderItem->quantity,
                    'shipping_contact_information_name' => $this->shippingContactInformation->name,
                    'shipping_contact_information_company_name' => $this->shippingContactInformation->company_name,
                    'shipping_contact_information_company_number' => $this->shippingContactInformation->company_number,
                    'shipping_contact_information_address' => $this->shippingContactInformation->address,
                    'shipping_contact_information_address2' => $this->shippingContactInformation->address2,
                    'shipping_contact_information_zip' => $this->shippingContactInformation->zip,
                    'shipping_contact_information_city' => $this->shippingContactInformation->city,
                    'shipping_contact_information_state' => $this->shippingContactInformation->state,
                    'shipping_contact_information_country' => $this->shippingContactInformation->country->iso_3166_2 ?? '',
                    'shipping_contact_information_email' => $this->shippingContactInformation->email,
                    'shipping_contact_information_phone' => $this->shippingContactInformation->phone,
                    'billing_contact_information_name' => $this->billingContactInformation->name,
                    'billing_contact_information_company_name' => $this->billingContactInformation->company_name,
                    'billing_contact_information_company_number' => $this->billingContactInformation->company_number,
                    'billing_contact_information_address' => $this->billingContactInformation->address,
                    'billing_contact_information_address2' => $this->billingContactInformation->address2,
                    'billing_contact_information_zip' => $this->billingContactInformation->zip,
                    'billing_contact_information_city' => $this->billingContactInformation->city,
                    'billing_contact_information_state' => $this->billingContactInformation->state,
                    'billing_contact_information_country' => $this->billingContactInformation->country->iso_3166_2 ?? '',
                    'billing_contact_information_email' => $this->billingContactInformation->email,
                    'billing_contact_information_phone' => $this->billingContactInformation->phone,
                    'shipping_method_name' => $this->shippingMethod->name ?? $this->shipping_method_name ?? 'Dummy',
                    'tags' => $this->tags->pluck('name')->join(',')
                ];
            }
        }

        return $resource;
    }

    public static function columns(): array
    {
        return [
            'order_number',
            'status',
            'sku',
            'quantity',
            'shipping_contact_information_name',
            'shipping_contact_information_company_name',
            'shipping_contact_information_company_number',
            'shipping_contact_information_address',
            'shipping_contact_information_address2',
            'shipping_contact_information_zip',
            'shipping_contact_information_city',
            'shipping_contact_information_state',
            'shipping_contact_information_country',
            'shipping_contact_information_email',
            'shipping_contact_information_phone',
            'billing_contact_information_name',
            'billing_contact_information_company_name',
            'billing_contact_information_company_number',
            'billing_contact_information_address',
            'billing_contact_information_address2',
            'billing_contact_information_zip',
            'billing_contact_information_city',
            'billing_contact_information_state',
            'billing_contact_information_country',
            'billing_contact_information_email',
            'billing_contact_information_phone',
            'shipping_method_name',
            'tags'
        ];
    }
}
