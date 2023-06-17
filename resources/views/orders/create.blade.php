@extends('layouts.app')
@section('content')
    <div class="container-fluid ">
        <div class="row">
            <div></div>
            <div class="col-xl-12 order-xl-1">
                <div class="mt-3 header-body text-black">
                    <span class="font-weight-600 font-text-lg">{{ __('Orders') . '/' }}</span><span
                        class="font-weight-400 font-md">{{ __('Create') }}</span>
                </div>
                <form method="post" action="{{ route('order.store') }}" autocomplete="off" data-type="POST"
                      enctype="multipart/form-data">
                    <div class="py-3">
                        @csrf
                        <div class="nav-wrapper">
                            <ul class="nav nav-pills nav-fill flex-md-row" id="editFormTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0 active" id="order-information-tab"
                                       data-toggle="tab"
                                       href="#order-information-tab-content" role="tab"
                                       aria-controls="tabs-icons-text-1"
                                       aria-selected="true">
                                        {{ __('General Information') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="order-address-information-tab"
                                       data-toggle="tab"
                                       href="#order-address-information-tab-content" role="tab"
                                       aria-controls="tabs-icons-text-3" aria-selected="false">
                                        {{ __('Address Information') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="order-shipment-tab" data-toggle="tab"
                                       href="#order-shipment-tab-content" role="tab" aria-controls="tabs-icons-text-1"
                                       aria-selected="true">
                                        {{ __('Shipment') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="order-details-tab" data-toggle="tab"
                                       href="#order-details-tab-content" role="tab"
                                       aria-controls="tabs-icons-text-2" aria-selected="false">
                                        {{ __('Order Details') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content text-black bg-formWhite border-12 p-3">
                            <div class="tab-pane fade show active" id="order-information-tab-content" role="tabpanel"
                                 aria-labelledby="order-information-tab">
                                <div class="d-flex justify-content-md-between">
                                    <div class="flex-grow-1 d-lg-flex">
                                        <div class="form-group mb-0 col-lg-3 text-left my-3">
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
                                                <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
                                            @endif
                                            @include('shared.forms.input', [
                                                'name' => 'number',
                                                'containerClass' => 'flex-grow-1 mx-0',
                                                'label' => __('Number'),
                                                'type' => 'text',
                                                'error' => ! empty($errors->get('number')) ? $errors->first('number') : false,
                                                'value' => old('number') ?? $order->number ?? ''
                                            ])

                                            <div class="flex-grow-1 searchSelectCustomer">
                                                <div class="w-100 mt-3">
                                                    <div class="form-group">
                                                        <label class="form-control-label">{{ __('Order Status') }}</label>

                                                        @if ($errors->get('order_status_id'))
                                                            <span class="text-danger form-error-messages font-weight-600 font-xs">
                                                                &nbsp;&nbsp;&nbsp; {{ $errors->first('order_status_id') }}
                                                            </span>
                                                        @endif

                                                        <select name="order_status_id" class="form-control enabled-for-customer" data-toggle="select" data-placeholder="">
                                                            <option value="{{$order->orderStatus->id ?? old('order_status_id') ?? 'chooseHere'}}">
                                                                {{ $order->orderStatus->name ?? '' }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="w-100 mt-3">
                                                    <div class="form-group">
                                                        <label class="form-control-label mb-0">{{ __('Tags') }}</label>
                                                        @include('orders.sections._tags')
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex mt-5 mx-2 flex-wrap ">
                                                <div class="custom-form-checkbox mr-3 d-flex d-lg-block">
                                                    @include('shared.forms.checkbox', [
                                                        'name' => 'address_hold',
                                                        'label' => __('Address hold'),
                                                        'checked' => old('address_hold') ??  false,
                                                    ])
                                                    @include('shared.forms.checkbox', [
                                                        'name' => 'fraud_hold',
                                                        'label' => __('Fraud hold'),
                                                        'containerClass' => ' ml-2 ml-lg-0',
                                                        'checked' => old('fraud_hold') ?? false,
                                                    ])
                                                </div>
                                                <div class="custom-form-checkbox d-flex d-lg-block">
                                                    @include('shared.forms.checkbox', [
                                                        'name' => 'operator_hold',
                                                        'label' => __('Operator hold'),
                                                        'containerClass' => 'ml-0  ml-lg-0',
                                                        'checked' =>  old('operator_hold') ??  false,
                                                    ])
                                                    @include('shared.forms.checkbox', [
                                                        'name' => 'payment_hold',
                                                        'label' => __('Payment hold'),
                                                        'containerClass' => 'ml-2 ml-lg-0',
                                                        'checked' => old('payment_hold') ?? false,
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-0 col-lg-6 text-left my-3 ml-lg-5">
                                            <h3>{{ __('Notes') }}</h3>
                                            <div class="textarea-section">
                                                <label for="packer-notes" class="form-control-label text-neutral-text-gray font-weight-600 font-xs ">{{ __('Note to pack') }}</label>
                                                <textarea id="packer-notes" name="packing_note" class="form-control text-black"></textarea>
                                                <label for="slip-notes" class="form-control-label text-neutral-text-gray font-weight-600 font-xs mt-3 ">{{ __('Slip note') }}</label>
                                                <textarea id="slip-notes" name="slip_note" class="form-control text-black"></textarea>
                                                <label for="gift-notes" class="form-control-label text-neutral-text-gray font-weight-600 font-xs mt-3 ">{{ __('Gift note') }}</label>
                                                <textarea id="gift-notes" name="gift_note" class="form-control text-black"></textarea>
                                                <label for="internal-notes" class="form-control-label text-neutral-text-gray font-weight-600 font-xs mt-3 ">{{ __('Internal note') }}</label>
                                                <textarea id="internal-notes" name="internal_note" class="form-control text-black"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button"
                                            class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white"
                                            data-id="#order-address-information-tab">
                                        {{ __('Next') }}
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="order-address-information-tab-content" role="tabpanel"
                                 aria-labelledby="order-address-information-tab">
                                <div class="w-100">
                                    <div class="">
                                        <h6 class="heading-small text-muted mb-4 mx-2">{{ __('Shipping information') }}</h6>
                                        @include('shared.forms.contactInformationFields', [
                                            'name' => 'shipping_contact_information',
                                            'contactInformation' => $order->shippingContactInformation ?? ''
                                        ])
                                        <div class="custom-form-checkbox mt-2 mx-2">
                                            <div>
                                                <input class="" name="differentBillingInformation" id="fill-information" type="checkbox" value="1">
                                                <label class="text-black font-weight-600" for="fill-information">{{ __('Different billing information') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-100 billing_contact_information mt-5">
                                        <h6 class="heading-small text-muted mb-4 border-top-gray mx-2">{{ __('Billing information') }}</h6>
                                        @include('shared.forms.contactInformationFields', [
                                            'name' => 'billing_contact_information',
                                            'contactInformation' => $order->billingContactInformation ?? ''
                                        ])
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button"
                                            class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white"
                                            data-id="#order-shipment-tab">
                                        {{ __('Next') }}
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="order-shipment-tab-content" role="tabpanel"
                                 aria-labelledby="order-shipment-tab">
                                <div class="w-50 searchSelect">
                                    <div class="form-group mb-0 mx-2 text-left mb-3">
                                        @include('shared.forms.new.ajaxSelect', [
                                            'url' => route('shipping_method_mapping.filterShippingMethods'),
                                            'name' => 'shipping_method_id',
                                            'className' => 'ajax-user-input shipping_method_id',
                                            'placeholder' => __('Select a shipment method'),
                                            'error' => !empty($errors->get('shipping_method_id')) ? $errors->first('shipping_method_id') : false,
                                            'label' => __('Shipments'),
                                            'default' => [
                                                'id' => old('shipping_method_id'),
                                                'text' => ''
                                            ],
                                            'fixRouteAfter' => '.ajax-user-input.customer_id'
                                        ])
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button"
                                            class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white "
                                            data-id="#order-details-tab">
                                        {{ __('Next') }}
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="order-details-tab-content" role="tabpanel"
                                 aria-labelledby="order-details-tab">
                                 <div class="mx-2 border-bottom-gray mb-4">
                                     <h6 class="heading-small text-muted text-left mb-2"> {{ __('Add Products To Order') }}</h6>
                                 </div>
                                @if(!empty(! empty($errors->get('order_items'))))
                                    <span class="text-danger form-error-messages font-weight-600 font-xs">&nbsp;&nbsp;&nbsp;{{ $errors->first('order_items') }}</span>
                                @endif
                                <div class="searchSelect">
                                    @include('shared.forms.ajaxSelect',
                                        [
                                            'url' => route('order.filterProducts'),
                                            'className' => 'ajax-user-input order-item-input',
                                            'placeholder' => __('Search Products'),
                                            'labelClass' => 'd-block',
                                            'label' => ''
                                        ])
                                </div>
                                <div class="table-responsive items-table searchedProducts">
                                    <table class="col-12 table align-items-center table-flush">
                                        <thead>
                                        <tr>
                                            <th scope="col">{{ __('Image') }}</th>
                                            <th scope="col" class="px-6">{{ __('Product') }}</th>
                                            <th scope="col">{{ __('Unit Price') . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}</th>
                                            <th scope="col">{{ __('Quantity') }}</th>
                                            <th scope="col">{{ __('Pending') }}</th>
                                            <th scope="col">{{ __('Allocated') }}</th>
                                            <th scope="col">{{ __('Shipped') }}</th>
                                            <th scope="col">{{ __('Returned') }}</th>
                                            <th scope="col">{{ __('Total Price') . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}</th>
                                            <th scope="col">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody id="item_container">
                                        @if(count( old()['order_items'] ?? [] ) > 0)
                                            @foreach(old()['order_items'] as $key => $orderItem)
                                                <tr class="productRow {{ $orderItem['is_kit_item'] == 'false' ? 'parentProductRow' : '' }}" data-index="{{ $key }}">
                                                    <input type="hidden" name="order_items[{{ $key }}][parent_product_id]" value="{{ $orderItem['product_id'] }}"/>
                                                    <td class="{{ ((isset($orderItem['cancelled'])) ? 'px-5' : null) }}">
                                                        @if (! empty($orderItem['first_product_image_source']))
                                                            <img src="{{ $orderItem['first_product_image_source'] }}" alt="">
                                                            <input type="hidden" name="order_items[{{ $key }}][img]" value="{{ $orderItem['first_product_image_source'] }}">
                                                        @else
                                                            <img src="{{ asset('img/no-image.png') }}" alt="No image">
                                                        @endif
                                                    </td>
                                                    <td class="px-6">
                                                        <input type="hidden" name="order_items[{{ $key }}][is_kit_item]" value="{{ ((isset($orderItem['cancelled'])) ? 'true' : 'false') }}">
                                                        SKU: {{ $orderItem['sku'] }} </br>
                                                        Name: <a href="{{ route('product.edit', $orderItem['id']) }}" target="_blank">{!! $orderItem['name'] !!}</a>
                                                        <input type="hidden" name="order_items[{{ $key }}][text]" value="{{ $orderItem['text'] }}">
                                                        <input type="hidden" name="order_items[{{ $key }}][name]" value="{{ $orderItem['name'] }}">
                                                        <input type="hidden" name="order_items[{{ $key }}][sku]" value="{{ $orderItem['sku'] }}">
                                                        <input type="hidden" name="order_items[{{ $key }}][id]" value="{{ $orderItem['id'] }}">
                                                        <input type="hidden" class="order-item-{{ $orderItem['product_id'] }}" value="{{ $orderItem['cancelled'] ?? '' }}" name="order_items[{{ $key }}][cancelled]">
                                                    </td>
                                                    <td class="product-price">
                                                        <span class="price-value">{{ $orderItem['price'] }}</span>
                                                        <input type="hidden" name="order_items[{{ $key }}][price]" value="{{ $orderItem['price'] }}">
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                                                            <input type="hidden" name="order_items[{{ $key }}][child_quantity]" value="{{ $orderItem['child_quantity'] ?? '' }}"/>
                                                            <input type="number" data-quantity="{{ $orderItem['child_quantity'] ?? '' }}" readonly
                                                                   class="quantity-input form-control font-weight-600 px-2 py-1 childquantity-input_{{$key}}"
                                                                   name="order_items[{{ $key }}][quantity]" value="{{ $orderItem['quantity'] }}"/>
                                                        </div>
                                                    </td>
                                                    <td>0</td>
                                                    <td>{{ $orderItem['quantity_allocated'] ?? 0 }}</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td class="{{ $orderItem['is_kit_item'] == 'false' ? 'item-total-price' : '' }}"></td>
                                                    @if(($orderItem['cancelled'] ?? []) == 1)
                                                        <td>
                                                            <input type="hidden" name="order_items[{{ $key }}][product_id]" value="{{ $orderItem['product_id'] }}"/>
                                                        </td>
                                                    @elseif( ($orderItem['cancelled'] ?? []) == 0 )
                                                        <td>
                                                            <input type="hidden" name="order_items[{{ $key }}][product_id]" value="{{ $orderItem['product_id'] }}"/>
                                                            <button type="button" class="text-white mx-auto px-4 py-2 mr-1 border-0 cancelOrderKit cancelOrderKit{{ $orderItem['product_id'] }}"
                                                                    data-id="{{ $orderItem['product_id'] }}" data-toggle="modal" data-target="#cancelKitItem">
                                                                Cancel
                                                            </button>
                                                        </td>
                                                    @else
                                                        <td class="delete-row productList">
                                                            <input type="hidden" name="order_items[{{ $key }}][product_id]" value="{{ $orderItem['product_id'] }}"/>
                                                            <button type="button" class="table-icon-button">
                                                                <i class="picon-trash-filled del_icon" title="Delete"></i>
                                                            </button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="w-100">
                                    <div class="w-100 d-flex justify-content-end">
                                        <table class="total-table">
                                            <tr>
                                                <td>{{ __('Subtotal') }}</td>
                                                <td class="subtotal-value"></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Total') }}</td>
                                                <td class="total-value"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button
                                        class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('shared.modals.orderKit')

@endsection

@push('js')
    <script>
        new Order();
    </script>
@endpush

