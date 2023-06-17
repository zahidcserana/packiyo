@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => 'Purchase Orders',
        'subtitle' => 'Receive Purchase Order'
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 card">
                    <div class="table-responsive p-4">
                        <div>
                            <input type="text" placeholder="Barcode" class="form-control orangeBorder focus-thief" id="barcode" />
                            <input type="text" class="focus-thief-sidekick" />
                        </div>
                        <form role="form" method="POST" action="{{ route('purchase_order.updatePurchaseOrder', compact('purchaseOrder')) }}">
                            @csrf
                        <table class="table align-items-center col-12 items-table" id="-received-purchase-order-items-table">
                            <thead>
                                <tr class="text-center">
                                    <td>{{__('Product')}}</td>
                                    <td>{{__('Lot')}}</td>
                                    <td>{{__('Location')}}</td>
                                    <td>{{__('Ordered')}}</td>
                                    <td>{{__('Received')}}</td>
                                    <td>{{__('Rejected')}}</td>
                                    <td>{{__('Accepted')}}</td>
                                </tr>
                            </thead>
                            <tbody style="cursor:pointer">
                                @foreach($purchaseOrder->purchaseOrderItems as $item)
                                    <tr class="text-center">
                                        <td class="wrap">
                                            <div class="row align-items-center">
                                                <div class="col-sm-4">
                                                    @if (empty($item->product->productImages[0]))
                                                        <img src="{{ asset('img/no-image.png') }}" alt="No image">
                                                    @else
                                                        <img src="{{ $item->product->productImages[0]->source }}" width="30%">
                                                    @endif
                                                </div>
                                                <div class="col-sm-8">
                                                    <span>{{ $item->product->name }}</span><br>
                                                    <span>SKU: {{ $item->product->sku }}</span>
                                                    <a href="{{ route('product.barcode', ['product' => $item->product]) }}" target="_blank" class="table-icon-button">
                                                        <i class="picon-printer-light icon-lg align-middle"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            @if($item->product->lot_tracking == 1)
                                                <div class="searchSelect pt-5" id="lot_id_container_{{ $item->id }}">
                                                    @include('shared.forms.select', [
                                                       'name' => 'lot_id[' . $item->id . ']',
                                                       'className' => 'ajax-user-input',
                                                       'placeholder' => __('Search for a lot'),
                                                       'error' => ! empty($errors->get('lot_id')) ? $errors->first('lot_id') : false,
                                                       'value' => '',
                                                       'options' => $item->product->lots->pluck('name', 'id')->toArray()
                                                    ])
                                                </div>
                                                <div id="lot_name_container_{{ $item->id }}">

                                                </div>
                                                <a href="#editLotsModal" data-toggle="modal" data-id="{{ $item->id }}" data-product="{{ $item->product->id }}" class="btn btn-link" type="button">{{__('Create a new lot')}}</a>
                                            @endif
                                        </td>
                                        <td>
                                            <br>
                                            <div class="searchSelect">
                                            @include('shared.forms.new.ajaxSelect', [
                                                       'url' => route('purchase_order.filterLocations'),
                                                       'name' => 'location_id[' . $item->id . ']',
                                                       'className' => 'ajax-user-input',
                                                       'placeholder' => __('Search for a location'),
                                                       'label' => '',
                                                       'default'=> (count($purchaseOrder->warehouse->locations) ? ['id'=>$purchaseOrder->warehouse->locations[0]['id'], 'text'=>$purchaseOrder->warehouse->locations[0]['name']] : [] )
                                                   ])
                                            </div>
                                        </td>
                                        <td>
                                            {{ $item->quantity }}
                                        </td>
                                        <td>
                                            {{ $item->quantity_received }}
                                        </td>
                                        <td>
                                            <a href="#quantityRejectedModal" data-toggle="modal" data-id="{{ $item->id }}" class="btn btn-link">
                                                {{ $item->quantity_rejected }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="form-group mb-0 mx-2 text-left mb-3">
                                                <label for="quantity_received[{{ $item->id }}]"
                                                       data-id="quantity_received.{{ $item->id }}"
                                                       class="text-neutral-text-gray font-weight-600 font-xs">
                                                </label>
                                                <div
                                                    class="input-group input-group-alternative input-group-merge tableSearch">
                                                    <input
                                                        barcode="{{ $item->product->barcode }}"
                                                        class="form-control font-weight-600 h-auto p-2"
                                                        type="number"
                                                        name="quantity_received[{{ $item->id }}]"
                                                        value="0"
                                                    >
                                                    <input type="hidden" name="lot_tracking[{{ $item->id }}]" id="lot_tracking_{{ $item->id }}" value="{{intval($item->product->lot_tracking)}}"/>
                                                    <input type="hidden" name="lot_name[{{ $item->id }}]" id="lot_name_{{ $item->id }}" value=""/>
                                                    <input type="hidden" name="expiration_date[{{ $item->id }}]" id="expiration_date_{{ $item->id }}" value=""/>
                                                    <input type="hidden" name="supplier_id[{{ $item->id }}]" id="supplier_id_{{ $item->id }}" value="0"/>
                                                    <input type="hidden" name="product_id[{{ $item->id }}]" id="product_id_{{ $item->id }}" value="{{ $item->product->id }}"/>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-12 text-center">
                            <button type="submit"
                                    class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button"
                                    id="submit-button">
                                {{ __('Save') }}
                            </button>
                        </div>
                        </form>
                    </div>
            </div>
        </div>
    </div>
    @include('shared.modals.rejectedPurchaseOrderItemModal')
    @include('shared.modals.editLotsModal')
@endsection

@push('js')
    <script>
        new PurchaseOrder;
    </script>
@endpush
