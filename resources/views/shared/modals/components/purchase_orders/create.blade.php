<div class="modal fade confirm-dialog" id="purchaseOrderCreateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form method="post" action="{{ route('purchase_orders.store') }}" autocomplete="off" data-type="POST" id="purchase-order-create-form" enctype="multipart/form-data"
              class="modal-content purchaseOrderForm">
            @csrf
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Create purchase order') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-center py-3">
                <div class="d-lg-flex justify-content-md-between">
                    <div class="w-50">
                        @if(!isset($sessionCustomer))
                            <div class="searchSelect">
                                @include('shared.forms.new.ajaxSelect', [
                                'url' => route('user.getCustomers'),
                                'name' => 'customer_id',
                                'className' => 'ajax-user-input customer_id',
                                'placeholder' => __('Select customer'),
                                'label' => __('Customer'),
                                'default' => [
                                    'id' => old('customer_id'),
                                    'text' => ''
                                ],
                                'fixRouteAfter' => '.ajax-user-input.customer_id'
                            ])
                            </div>
                        @else
                            <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" />
                        @endif
                        <div class="searchSelect">
                            @include('shared.forms.new.ajaxSelect', [
                            'url' => route('purchase_order.filterWarehouses'),
                            'name' => 'warehouse_id',
                            'className' => 'ajax-user-input warehouse_id enabled-for-customer',
                            'placeholder' => __('Enter Warehouse'),
                            'label' => __('Warehouse'),
                            'default' => [
                                'id' => old('warehouse_id'),
                                'text' => ''
                            ]
                        ])
                        </div>
                        <div class="searchSelect">
                            @include('shared.forms.new.ajaxSelect', [
                            'url' => route('purchase_order.filterSuppliers'),
                            'name' => 'supplier_id',
                            'className' => 'ajax-user-input supplier_id enabled-for-customer',
                            'placeholder' => __('Enter Vendor'),
                            'label' => __('Vendor'),
                            'default' => [
                                'id' => old('supplier_id'),
                                'text' => ''
                            ],
                            'fixRouteAfter' => '.ajax-user-input.customer_id'
                        ])
                        </div>
                        <div class="form-group mb-0 mx-2 text-left mb-3">
                            <label for=""
                                   data-id="number"
                                   class="text-neutral-text-gray font-weight-600 font-xs">{{ __('PO Number') }} </label>
                            <div
                                class="input-group input-group-alternative input-group-merge">
                                <input
                                    class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                    placeholder="{{ __('PO Number') }}"
                                    type="text"
                                    name="number"
                                >
                            </div>
                        </div>
                        @include('shared.forms.editSelectTag', [
                                'containerClass' => 'form-group mb-0 mx-2 text-left mb-3',
                                'labelClass' => '',
                                'selectClass' => 'select-ajax-tags',
                                'label' => __('Tags'),
                                'minimumInputLength' => 3,
                                'default' => []
                            ])
                    </div>
                    <div class="w-50">
                        <div class="form-group mb-0 mx-2 text-left mb-3">
                            <label for=""
                                   data-id="tracking_number"
                                   class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking number') }} </label>
                            <div
                                class="input-group input-group-alternative input-group-merge ">
                                <input
                                    class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                    placeholder="{{ __('Tracking number') }}"
                                    type="text"
                                    name="tracking_number"
                                >
                            </div>
                        </div>
                        <div class="form-group mb-0 mx-2 text-left mb-3">
                            <label for=""
                                   data-id="tracking_url"
                                   class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking URL') }} </label>
                            <div
                                class="input-group input-group-alternative input-group-merge">
                                <input
                                    class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                    placeholder="{{ __('Tracking URL') }}"
                                    type="text"
                                    name="tracking_url"
                                >
                            </div>
                        </div>
                        <div class="form-group mb-0 mx-2 text-left mb-3">
                            <label for=""
                                   data-id="ordered_at"
                                   class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Ordered at') }} </label>
                            <div
                                class="input-group input-group-alternative input-group-merge">
                                <input
                                    class="form-control font-weight-600 text-neutral-gray h-auto p-2  datetimepicker"
                                    placeholder="{{ __('Ordered at') }}"
                                    type="text"
                                    name="ordered_at"
                                >
                            </div>
                        </div>
                        <div class="form-group mb-0 mx-2 text-left mb-3">
                            <label for=""
                                   data-id="expected_at"
                                   class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Expected at') }} </label>
                            <div
                                class="input-group input-group-alternative input-group-merge">
                            <input
                                    class="form-control font-weight-600 text-neutral-gray h-auto p-2 datetimepicker"
                                    placeholder="{{ __('Expected at') }}"
                                    type="text"
                                    name="expected_at"
                                >
                            </div>
                        </div>
                    </div>
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
                <div class="searchSelect" id="createOrderItemsSelection">
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
                        @if(count( old()['purchase_order_items'] ?? [] ) > 0)
                            @foreach(old()['purchase_order_items'] as $key => $orderItem)
                                <tr class="productRow" data-index="{{ $key }}">
                                    <td>
                                        {!! $orderItem['text'] !!}
                                        <input type="hidden" name="purchase_order_items[{{ $key }}][product_id]" value="{{ $orderItem['id'] }}">
                                    </td>
                                    <td>
                                        {!! $orderItem['barcode'] !!}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                            <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity]" value="{{ $orderItem['quantity'] }}"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                            <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity_sell_ahead]" value="{{ $orderItem['quantity_sell_ahead'] }}"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                            <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[{{ $key }}][quantity_to_receive]" value="{{ $orderItem['quantity_received'] }}"/>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit"
                        class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-submit-button"
                        id="submit-button">{{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
