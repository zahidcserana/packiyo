<tr
    id="order_item_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}"
    class="order_item_row"
    package="0"
    location="LOCATION-ID"
    rel="{{$orderItem->id}}"
    key="{{$key}}"
    barcode="{{$orderItem->product->barcode}}"
    picked-location-id="{{$toteOrderItemLocationId}}"
    picked-location-name="{{$toteOrderItemLocationName}}"
    picked-tote-id="{{$toteOrderItemToteId}}"
    picked-tote-name="{{$toteOrderItemToteName}}"
    has-serial-number="{{ $orderItem->product['has_serial_number'] }}"
    product-type="{{ $orderItem->product->kit_type }}"
    @if($orderItem->parentOrderItem)
        parent-id="{{ $orderItem->parentOrderItem->id }}"
    @endif
    @foreach($orderItem->kitOrderItems as $kitOrderItem)
        to-pack-per-kit-{{$kitOrderItem->id}}="{{$kitOrderItem->quantity / $orderItem->quantity}}"
        packed-per-kit-{{$kitOrderItem->id}}="0"
    @endforeach
>
    <td class="col-6 wrap @if($orderItem->parentOrderItem) pl-5 @endif">
        <div class="row">
            <div class="col-6 col-xl-3">
                <img src="{{!empty($orderItem->product->productImages) && isset($orderItem->product->productImages[0]) ? $orderItem->product->productImages[0]->source : '/img/no-image.png' }}" class="img-thumbnail" />
            </div>
            <div class="col-6 col-xl-9">
                {{$orderItem->product->name}}<br />
                <a href="{{ route('product.edit', ['product' => $orderItem->product]) }}" target="_blank">{{$orderItem->product->sku}}</a><br />
                <span class="order_item_serial_number"></span>
            </div>
        </div>
    </td>
    <td class="col-4 wrap">
        <div id="order_item_location_span_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" class="col-12">
            @if($toteOrderItem != null)
                <button class="btn white-space-normal w-100 text-left" type="button" rel="item_{{$orderItem->id}}_picked" id="item_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}_picked" value="{{$toteOrderItemLocationId}}">
                    {{!is_null($toteOrderItemToteName)?$toteOrderItemToteName.' - ':''}}{{$toteOrderItemLocationName}}<span id="order_item_pick_{{$toteOrderItemLocationId}}_{{$orderItem->id}}_{{$toteOrderItemToteId}}" class="picked_{{$orderItem->id}} d-none">{{$quantityToPickFromRow}}</span>
                </button>
                <input type="hidden" id="order_item_pick_max_{{$toteOrderItemLocationId}}_{{$orderItem->id}}_{{$toteOrderItemToteId}}" value="{{$quantityToPickFromRow}}"/>
            @else
                @if($orderItem->product->kit_type == \App\Models\Product::PRODUCT_TYPE_REGULAR)
                    <select id="item_{{$orderItem->id}}_locations" class="form-control font-weight-400 text-black h-auto p-2">
                        @foreach($orderItem->product->locations as $location)
                            @if($location->pivot->quantity_on_hand > 0 && ($location->pickable_effective || !empty($bulkShipBatch)))
                                <option value="{{$location->id}}">{{$location->name}} - {{$location->pivot->quantity_on_hand}}</option>
                            @endif
                        @endforeach
                    </select>
                @endif
            @endif
        </div>
        @foreach($orderItem->product->locations as $location)
            @if($location->pivot->quantity_on_hand > 0 && ($location->pickable_effective || !empty($bulkShipBatch)))
                <input type="hidden" id="order_item_quantity_beginning_{{$location->id}}_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="{{$location->pivot->quantity_on_hand}}" rel="{{$orderItem->id}}"/>
            @endif
        @endforeach
        <input type="hidden" id="order_item_quantity_form_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="{{$quantityToPickFromRow}}" rel="{{$orderItem->id}}" class="item_quantity"/>
        <input type="hidden" id="order_item_id_form_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="{{$orderItem->id}}"/>
        <input type="hidden" id="order_item_location_form_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="LOCATION-ID"/>
        <input type="hidden" id="order_item_tote_form_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="TOTE-ID"/>
        <input type="hidden" id="order_item_weight_form_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="{{floatval($orderItem->product->weight)}}"/>
        <input type="hidden" id="order_item_serial_number_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}" name="" value="SERIAL-NUMBER"/>
    </td>
    <td class="col-1">
        <span id="order_item_quantity_span_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}">{{$quantityToPickFromRow}} @if($orderItem->product->kit_type == \App\Models\Product::PRODUCT_TYPE_STATIC_KIT) {{ trans_choice('kit|kits', $quantityToPickFromRow) }} @endif</span>
    </td>
    <td class="col-1">
        @if(!$orderItem->kitOrderItems->count())
            <a class="btn btn-icon bg-logoOrange borderOrange pack-item-button" href="#" role="button"><i class="picon-download-light text-white"></i></a>
            <a
                id="order_item_unpack_LOCATION-ID_{{$orderItem->id}}_{{$toteOrderItemLocationId}}_{{$toteOrderItemToteId}}"
                class="btn btn-icon bg-logoOrange borderOrange unpack-item-button"
            ><i class="picon-upload-light text-white"></i></a>
        @endempty
        
        <div class="btn-group dropdown">
            <button type="button" class="btn btn-sm btn-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="picon-more-vertical-filled icon-lg"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('product.edit', ['product' => $orderItem->product]) }}" target="_blank">
                    {{ __('View info') }}
                </a>
                <x-print_modal_button
                    class="dropdown-item"
                    submit-action="{{ route('product.barcodes', $orderItem->product) }}"
                    pdf-url="{{ route('product.barcode', $orderItem->product) }}"
                    customer-printers-url="{{ route('product.getCustomerPrinters', $orderItem->product) }}"
                >
                    {{ __('Print product barcode') }}
                </x-print_modal_button>
            </div>
        </div>
    </td>
</tr>
