@extends('layouts.app', ['title' => __('Purchase Order Management')])

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Purchase Order',
        'subtitle' =>  __('Edit Purchase Order'),
    ])
    <div class="container-fluid  select2Container formsContainer" id="globalForm" data-form-action="{{ route('purchase_orders.update', ['purchase_order' => $purchaseOrder]) }}"  data-type="PUT">
        <form action="{{ route('purchase_orders.update', ['purchase_order' => $purchaseOrder]) }}" autocomplete="off" data-type="PUT" enctype="multipart/form-data"
              class="smallForm productForm card px-3 py-5 border-8" id="purchase-order-create-form">
            @csrf
            {{ method_field('PUT') }}
            <a href="{{route('purchase_orders.index')}}" class="btn btn-sm bg-logoOrange text-white mx-3 corner-back-button d-flex align-items-center">
                <i class="picon-chevron-double-backward-filled icon-white"></i>
                {{__('All Purchase Orders')}}
            </a>
            <div class="d-flex pb-3">
                <span class="modal-title text-black text-left">{{ __('Purchase Order Details') }}</span>
                @include('shared.buttons.sectionEditButtons', ['saveButtonId' => 'submit-international'])
            </div>
            <div class="row">
                @include('purchase_orders.purchaseOrderInformationFields', [
                    'purchaseOrder' => $purchaseOrder
                ])
            </div>
            <div class="pl-lg-4">
                <div class="searchSelect text-center" id="createOrderItemsSelection">
                    <label data-id="purchase_order_items"></label>
                    @include('shared.forms.ajaxSelect', [
                        'url' => route('purchase_order.filterProducts'),
                        'name' => 'purchase_order_items[0][product_id]',
                        'className' => 'ajax-user-input product_id',
                        'placeholder' => __('Search products'),
                        'label' => '',
                        'labelClass' => 'd-block',
                        'fixRouteAfter' => '.ajax-user-input.customer_id',
                    ])
                </div>
                <div class="table-responsive table-overflow items-table searchedProducts">
                    <table class="col-12 table align-items-center table-flush">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('Product') }}</th>
                            <th scope="col">{{ __('Barcode') }}</th>
                            <th scope="col">{{ __('Quantity') }}</th>
                            <th scope="col">{{ __('Sell Ahead Quantity') }}</th>
                            <th scope="col">{{ __('Quantity Received') }}</th>
                        </tr>
                        </thead>
                        <tbody id="item_container">
                        @foreach($purchaseOrder->purchaseOrderItems as $key => $orderItem)
                            <input type="hidden" name="purchase_order_items[{{ $key }}][purchase_order_item_id]" value="{{ $orderItem->id }}">
                            <tr class="productRow" data-index="{{ $key }}">
                                <td>
                                    {{ __('SKU:') }} {{ $orderItem->product->sku }}
                                    {{ __('Name:') }} {{ $orderItem->product->name }}
                                    <input type="hidden" name="purchase_order_items[{{ $key }}][product_id]" value="{{ $orderItem->product->id }}">
                                </td>
                                <td>
                                    {!! $orderItem->product->barcode !!}
                                </td>
                                <td>
                                    @include('shared.forms.input', [
                                       'name' => 'purchase_order_items[' . $key . '][quantity]',
                                       'label' => '',
                                       'type' => 'number',
                                       'value' => $orderItem->quantity,
                                       'class' => 'reset_on_delete quantity ordered-quantity',
                                       'min' => 0
                                   ])
                                </td>
                                <td>
                                    @include('shared.forms.input', [
                                       'name' => 'purchase_order_items[' . $key . '][quantity_sell_ahead]',
                                       'label' => '',
                                       'type' => 'number',
                                       'value' => $orderItem->quantity_sell_ahead,
                                       'class' => 'reset_on_delete quantity sell-ahead-quantity'
                                       ,
                                       'min' => 0,
                                       'max' => $orderItem->quantity
                                   ])
                                </td>
                                <td>
                                    <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">
                                        <input type="number" class="quantityInput form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[{{ $key }}][quantity_received]" value="{{ $orderItem->quantity_received }}"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <button class="globalSave p-0 border-0 bg-logoOrange align-items-center" id="{{ $saveButtonId ?? '' }}" type="button">
            <i class="picon-save-light icon-white icon-xl" title="Save"></i>
        </button>
    </div>
@endsection

@push('js')
    <script>
        new PurchaseOrder;
    </script>
@endpush
