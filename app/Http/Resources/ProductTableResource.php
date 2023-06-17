<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTableResource extends JsonResource
{
    public function toArray($request): array
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['sku'] = $this->sku;
        $resource['name'] = $this->name;
        $resource['price'] = $this->price;
        $resource['notes'] = $this->notes;
        $resource['quantity'] = $this->quantity;
        $resource['quantity_on_hand'] = $this->quantity_on_hand;
        $resource['quantity_pending'] = $this->quantity_pending ?? null;
        $resource['quantity_available'] = $this->quantity_available;
        $resource['quantity_allocated'] = $this->isKit() ? '-' : $this->quantity_allocated;
        $resource['quantity_backordered'] = $this->isKit() ? '-' : $this->quantity_backordered;
        $resource['warehouse'] = $this->customer->contactInformation['name'];
        $resource['height'] = $this->height;
        $resource['width'] = $this->width;
        $resource['length'] = $this->length;
        $resource['weight'] = $this->weight;
        $resource['barcode'] = $this->barcode;
        $resource['hs_code'] = $this->hs_code;
        $resource['value'] = $this->value;
        $resource['date'] = user_date_time($this->created_at);
        $resource['is_kit'] = $this->isKit() ? __('Yes') : __('No');
        $resource['image'] = $this->productImages->first()->source ?? asset('img/no-image.png');
        $resource['tags'] = $this->tags->pluck('name')->join(', ');
        $resource['customer'] = [
            'name' => $this->customer->contactInformation->name,
            'url' => route('customer.edit', ['customer' => $this->customer]),
        ];
        $resource['link_edit'] = route('product.edit', ['product' => $this]);
        $resource['link_delete'] = [
            'token' => csrf_token(), 'url' => route('product.destroy', ['product' => $this]),
        ];
        $resource['is_deleted'] = (int) isset($this->deleted_at);

        $resource['print_barcode_button'] = view('components.print_modal_button', [
            'submitAction' => route('product.barcodes', $this),
            'pdfUrl' => route('product.barcode', $this),
            'customerPrintersUrl' => route('product.getCustomerPrinters', $this),
        ])->render();

        return $resource;
    }
}
