@extends('layouts.app', ['title' => __('Purchase Order Management')])

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Purchase Orders',
        'subtitle' =>  __('Create Purchase Orders')
    ])
    <div class="container-fluid ">
        <div class="row">
            <div class="col-xl-12 order-xl-1" id="globalForm">
                <form method="POST"
                      action="{{ route('purchase_orders.store') }}"
                      autocomplete="off"
                      data-type="POST"
                      id="purchase-order-create-form"
                      enctype="multipart/form-data"
                      class="purchaseOrderForm card px-3 py-4 border-8 productForm"
                      data-success-redirect="{{ route('purchase_orders.index') }}"
                >

                    @csrf
                    <a href="{{route('purchase_orders.index')}}" class="btn btn-sm bg-logoOrange text-white mx-3 corner-back-button d-flex align-items-center">
                        <i class="picon-chevron-double-backward-filled icon-white"></i>
                        {{__('All Purchase Orders')}}
                    </a>
                    <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id ?? '' }}" class="customer_id" />
                    <div class="py-3">
                        <div class="row">
                            @include('purchase_orders.purchaseOrderInformationFields')
                        </div>

                        <div class="d-lg-flex justify-content-md-between">
                            @foreach($additionalActions as $additionalAction)
                                <div>
                                    <button type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 additional-action" disabled
                                            id="add-all-{{$additionalAction}}-button" data-action="{{$additionalAction}}">{{ __('Add all '.$additionalAction) }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
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
                        <div class="table-responsive table-overflow items-table overflow-auto searchedProducts">
                            <table class="col-12 table align-items-center table-flush">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('Product') }}</th>
                                    <th scope="col">{{ __('Barcode') }}</th>
                                    <th scope="col">{{ __('Quantity') }}</th>
                                    <th scope="col">{{ __('Sell Ahead Quantity') }}</th>
                                </tr>
                                </thead>
                                <tbody id="item_container">
                                @if(count( old()['purchase_order_items'] ?? [] ) > 0)
                                    @foreach(old()['purchase_order_items'] as $key => $orderItem)
                                        <tr class="productRow" data-index="{{ $key }}">
                                            <td>
                                                {!! $orderItem['text'] !!}
                                                <input type="hidden" name="purchase_order_items[{{ $key }}][product_id]" value="{{ $orderItem['id'] }}">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                                    <input type="number" class="quantityInput form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity]" value="{{ $orderItem['quantity'] }}"/>
                                                    <div class="inputButtons">
                                                        <button type="button" class="up"><i class="fa fa-chevron-up"></i></button>
                                                        <button type="button" class="down"><i class="fa fa-chevron-down"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                                    <input type="number" class="quantityInput form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity_sell_ahead]" value="{{ $orderItem['quantity_sell_ahead'] }}"/>
                                                    <div class="inputButtons">
                                                        <button type="button" class="up"><i class="fa fa-chevron-up"></i></button>
                                                        <button type="button" class="down"><i class="fa fa-chevron-down"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                                    <input type="number" class="quantityInput form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[{{ $key }}][quantity_to_receive]" value="{{ $orderItem['quantity_received'] }}"/>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
                <button class="globalSave p-0 border-0 bg-logoOrange align-items-center" id="{{ $saveButtonId ?? '' }}" type="button">
                    <i class="picon-save-light icon-white icon-xl" title="Save"></i>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new PurchaseOrder;
    </script>
@endpush
