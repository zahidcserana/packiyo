@extends('layouts.app')
@section('content')
    <div class="container-fluid py-3 position-relative h-lg-100">
        @if (!empty($bulkShipBatch))
            <a href="{{route('bulk_shipping.index')}}" class="btn btn-sm bg-logoOrange text-white mx-3 left-top-edge d-flex align-items-center">
                <i class="picon-chevron-double-backward-filled icon-white"></i>
                {{__('All Batch Orders')}}
            </a>
        @else
            <a href="{{route('packing.index')}}" class="btn btn-sm bg-logoOrange text-white mx-3 left-top-edge d-flex align-items-center">
                <i class="picon-chevron-double-backward-filled icon-white"></i>
                {{__('All Orders')}}
            </a>
        @endif

        <form
            @if (!empty($bulkShipBatch))
                action="{{ route('bulk_shipping.ship', $bulkShipBatch) }}"
                data-success="{{ route('bulk_shipping.batches') }}"
                data-bulk-ship-batch="true"
            @else
                action="{{ route('packing.ship', ['order' => $order]) }}"
                data-success="{{ route('packing.index') }}"
                data-bulk-ship-batch="false"
            @endif
            method="POST"
            id="packing_form"
            class="h-lg-100"
        >
            @csrf
            <input type="hidden" name="packing_state" id="packing_state" value="" />
            <input type="hidden" name="customer_id" value="{{ $order->customer_id }}" />
            <div class="row h-lg-100">
                <div class="col-12 col-xl-6 mb-4 h-lg-100">
                    <div class="card p-4 h-lg-100 strech-container">
                        @if (!empty($bulkShipBatch))
                            <div class="row h-50">
                                <div class="col-12 mh-100">
                                    <div class="row flex-lg-grow-1 min-h-0 h-lg-100 my-2">
                                        <div class="col-12 h-lg-100 overflow-auto">
                                            <table class="table bulk-ship-orders">
                                                <thead>
                                                <tr>
                                                    <th>{{ __('Order Number') }}</th>
                                                    <th>{{ __('Shipping method') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($bulkShipBatch->orders as $bulkShipBatchOrder)
                                                    <tr data-id="{{ $bulkShipBatchOrder->id }}">
                                                        <td>
                                                            <a href="{{ route('order.edit', ['order' => $bulkShipBatchOrder]) }}" target="_blank">
                                                                {{ $bulkShipBatchOrder->number }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @include('shared.forms.select', [
                                                               'name' => "shipping_method_id[{$bulkShipBatchOrder->id}]",
                                                               'containerClass' => 'float-right w-100',
                                                               'label' => '',
                                                               'placeholder' => __('Shipping method'),
                                                               'error' => false,
                                                               'value' => $bulkShipBatchOrder->shipping_method_id ?? '',
                                                               'options' => ['dummy' => __('Dummy')] + $shippingMethods->pluck('carrierNameAndName', 'id')->all()
                                                            ])
                                                        </td>
                                                        <td class="bulk-ship-order-status">
                                                            @if ($bulkShipBatchOrder->pivot->shipped)
                                                                {{ __('Shipped') }}
                                                            @elseif ($bulkShipBatchOrder->pivot->errors)
                                                                {{ __('Failed') }}
                                                            @else
                                                                {{ __('Not shipped') }}
                                                                {{ $bulkShipBatchOrder->orderItems()->where('quantity_pending', '>', '0')->count() }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12 font-weight-600 font-md">
                                    {{ __('Order') }}
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-12 ">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="header-body text-black">
                                                <span class="font-weight-400 font-md"><a href="{{ route('order.edit', ['order' => $order]) }}" class="text-cyan" target="_blank">{{ $order->number }}</a></span>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="row flex-column">
                                                <div class="text-right text-lg-left">
                                                    {{__('Ship to')}}&nbsp;
                                                    <i class="picon-edit-filled icon-lg icon-orange" data-target="#shippingInformationEdit" data-toggle="modal"></i>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-12">
                                                        <div class="text-right text-lg-left">
                                                            <strong><span id="cont_info_name">{{ $order->shippingContactInformation->name ?? '' }}</span></strong>
                                                        </div>
                                                        <div class="d-none d-lg-block">
                                                            <span id="cont_info_email">{{ $order->shippingContactInformation->email ?? '' }}</span>
                                                            <br>
                                                            <span id="cont_info_phone">{{ $order->shippingContactInformation->phone ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 d-none d-lg-block">
                                                        <div>
                                                            @if( !empty($order->shippingContactInformation->company_name) || !empty($order->shippingContactInformation->company_number) )
                                                                <span id="cont_info_company_name">{{ $order->shippingContactInformation->company_name ?? '' }}</span>&nbsp;
                                                                <span id="cont_info_company_number">{{ $order->shippingContactInformation->company_number ?? '' }}</span>
                                                                <br>
                                                            @endif
                                                            <span id="cont_info_address">{{ $order->shippingContactInformation->address ?? '' }}</span>
                                                            <br>
                                                            @if( !empty($order->shippingContactInformation->address2) )
                                                                <span id="cont_info_address2">{{ $order->shippingContactInformation->address2 ?? '' }}</span>
                                                                <br>
                                                            @endif
                                                            <span id="cont_info_city">{{ $order->shippingContactInformation->city ?? '' }}</span>&nbsp;
                                                            <span id="cont_info_zip">{{ $order->shippingContactInformation->zip ?? '' }}</span>
                                                            <br>
                                                            <span id="cont_info_country_name">{{ $order->shippingContactInformation->country->name ?? '' }}</span>
                                                            <span id="cont_info_country_code" hidden>{{ $order->shippingContactInformation->country->iso_3166_2 ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row my-2">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <div class="input-group input-group-alternative input-group-merge mb-3">
                                        <input type="text" placeholder="Barcode" class="form-control font-weight-600 text-black h-auto p-2 focus-thief" id="barcode" autofocus />
                                    </div>
                                    <span>
                                        {{__('Items to pack')}}: <strong id="global_to_packed">0</strong>. {{__('Packed')}}: <strong id="global_packed">0</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row flex-lg-grow-1 min-h-0 h-lg-100 my-2">
                            <div class="col-12 h-lg-100 overflow-auto">
                                <table id="items_listing" class="table col-12 package-items-table unpacked-items-table">
                                    <thead>
                                        <tr>
                                            <th class="col-6">{{__('Item')}}</th>
                                            <th class="col-4">{{__('Location')}}</th>
                                            <th class="col-1">{{__('Quantity')}}</th>
                                            <th class="col-1">{{ __('Pack') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($order->orderItems as $key => $orderItem)
                                        <?php if($orderItem->order_item_kit_id) continue; ?>
                                        <?php if($orderItem->cancelled_at || $orderItem->quantity < 1) continue; ?>
                                        <?php $orderItemQuantityToPack = isset($bulkShipBatch) ? $orderItem->quantity_allocated : $orderItem->quantity_allocated_pickable; ?>
                                        @if($orderItemQuantityToPack > 0 || $orderItem->kitOrderItems->count())
                                            @if(isset($toteOrderItemArr[$orderItem->id]['locations']))
                                                @foreach($toteOrderItemArr[$orderItem->id]['locations'] as $locId => $data)
                                                    @include('packing.orderItemRow', [
                                                        'key' => $data['key'],
                                                        'orderItem' => $data['order_item'],
                                                        'toteOrderItem' => $data['tote_order_item'],
                                                        'toteOrderItemLocationId' => $data['tote_order_item']->location->id,
                                                        'toteOrderItemLocationName' => $data['tote_order_item']->location->name,
                                                        'toteOrderItemToteId' => $data['tote_id'],
                                                        'toteOrderItemToteName' => $data['tote_name'],
                                                        'quantityToPickFromRow' => $data['tote_order_item_quantity'] <= $orderItemQuantityToPack ? $data['tote_order_item_quantity'] : $orderItemQuantityToPack,
                                                        'bulkShipBatch' => $bulkShipBatch ?? null,
                                                    ])
                                                @endforeach
                                            @endif

                                            @if($toteOrderItemArr[$orderItem->id]['total_picked'] < $orderItemQuantityToPack || $orderItem->kitOrderItems->count())
                                                @include('packing.orderItemRow', [
                                                    'key' => $key,
                                                    'orderItem' => $orderItem,
                                                    'toteOrderItem' => null,
                                                    'toteOrderItemLocationId' => 0,
                                                    'toteOrderItemLocationName' => null,
                                                    'toteOrderItemToteId' => 0,
                                                    'toteOrderItemToteName' => null,
                                                    'quantityToPickFromRow' => $orderItem->kitOrderItems->count() ? $orderItem->quantity : $orderItemQuantityToPack - $toteOrderItemArr[$orderItem->id]['total_in_totes']
                                                ])

                                                @if($orderItem->kitOrderItems->count())
                                                    <input type="hidden" id="to-pack-per-kit-{{$orderItem->id}}" value="{{ $orderItem->kitOrderItems->sum('quantity') / $orderItem->quantity }}" />
                                                @endif
                                            @endif

                                            @foreach($orderItem->kitOrderItems as $kitOrderItemKey => $kitOrderItem)
                                                <?php $kitOrderItemQuantityToPack = isset($bulkShipBatch) ? $kitOrderItem->quantity_allocated : $kitOrderItem->quantity_allocated_pickable; ?>
                                                @if(isset($toteOrderItemArr[$kitOrderItem->id]['locations']))
                                                    @foreach($toteOrderItemArr[$kitOrderItem->id]['locations'] as $locId => $data)
                                                        @include('packing.orderItemRow', [
                                                            'key' => $data['key'],
                                                            'orderItem' => $data['order_item'],
                                                            'toteOrderItem' => $data['tote_order_item'],
                                                            'toteOrderItemLocationId' => $data['tote_order_item']->location->id,
                                                            'toteOrderItemLocationName' => $data['tote_order_item']->location->name,
                                                            'toteOrderItemToteId' => $data['tote_id'],
                                                            'toteOrderItemToteName' => $data['tote_name'],
                                                            'quantityToPickFromRow' => $data['tote_order_item_quantity'] <= $kitOrderItemQuantityToPack ? $data['tote_order_item_quantity'] : $kitOrderItemQuantityToPack,
                                                        ])
                                                    @endforeach
                                                @endif

                                                @if($toteOrderItemArr[$kitOrderItem->id]['total_picked'] < $kitOrderItemQuantityToPack)
                                                    @include('packing.orderItemRow', [
                                                        'key' => $key . '_' . $kitOrderItemKey,
                                                        'orderItem' => $kitOrderItem,
                                                        'toteOrderItem' => null,
                                                        'toteOrderItemLocationId' => 0,
                                                        'toteOrderItemLocationName' => null,
                                                        'toteOrderItemToteId' => 0,
                                                        'toteOrderItemToteName' => null,
                                                        'quantityToPickFromRow' => $kitOrderItemQuantityToPack - $toteOrderItemArr[$kitOrderItem->id]['total_in_totes']
                                                    ])
                                                @endif

                                                <input type="hidden" class="to-pack-total" id="to-pack-total-{{$kitOrderItem->id}}" value="{{$kitOrderItemQuantityToPack}}" />
                                                <input type="hidden" id="packed-total-{{$kitOrderItem->id}}" value="0" />
                                            @endforeach
                                        @endif

                                        <input type="hidden" class="to-pack-total @if($orderItem->kitOrderItems->count()) to-pack-total-skip-calculation @endif" id="to-pack-total-{{$orderItem->id}}" value="{{$orderItem->kitOrderItems->count() ? $orderItem->quantity_pending : $orderItemQuantityToPack}}" />
                                        <input type="hidden" id="packed-total-{{$orderItem->id}}" value="0" />
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if (empty($bulkShipBatch))
                            <div class="row my-2">
                            <div class="col-12 text-left">
                                <div id="shipping_method_container" class="row text-left align-items-center">
                                    <div class="col-12 col-md-6 col-xl-3 my-1">
                                        <i class="picon-alert-circled-light" data-toggle="tooltip" data-placement="top" data-html="true" title="{{ __('Method set on webshop: :method', ['method' => $order->shipping_method_name]) }}<br />{{ __('Service set on webshop: :service', ['service' => $order->shipping_method_code]) }}"></i>
                                        {{ __('Shipping method') }}
                                    </div>
                                    <div class="col-12 col-md-6 my-1">
                                        @include('shared.forms.select', [
                                           'name' => 'shipping_method_id',
                                           'containerClass' => 'float-right w-100',
                                           'label' => '',
                                           'placeholder' => __('Shipping method'),
                                           'error' => false,
                                           'value' => $order->shipping_method_id ?? '',
                                           'options' => ['dummy' => __('Dummy')] + $shippingMethods->pluck('carrierNameAndName', 'id')->all()
                                        ])
                                        @foreach($shippingMethods->pluck('settings.has_drop_points', 'id') as $id => $dropPoint)
                                            <input type="hidden" name="check-drop-point-{{ $id }}" id="check-drop-point-{{ $id }}" value="{{ $dropPoint }}">
                                        @endforeach
                                    </div>
                                    <div id="drop-point-modal" class="col-12 col-md-6 col-xl-3 my-1" hidden>
                                        <a
                                            href="#select-drop-point-modal"
                                            data-toggle="modal"
                                            data-target="#select-drop-point-modal"
                                            data-customer="{{ $order->customer_id }}"
                                            id="select-drop-points-button"
                                            class="btn bg-logoOrange mx-auto text-white w-100"
                                        >
                                            {{ __('Select Drop Point') }}
                                        </a>
                                        @include('shared.modals.components.selectDropPoint')
                                    </div>
                                    <div class="col-12 col-md-6 my-1" id="drop-point-info" hidden>
                                        <input type="hidden" name="drop_point_id" id="drop_point_id">
                                        <b>{{ __('Drop point:') }}</b> <span id="drop-point-details"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-xl-6 h-lg-100">
                    <div class="card p-4 h-lg-100 strech-container">
                        <div class="row my-2 {{ !empty($bulkShipBatchOrder) ? 'd-none' : '' }}">
                            <div class="col-lg-1">
                                <ul class="nav nav-pills nav-fill flex-lg-column">
                                    <li class="nav-item">
                                        <button type="button" id="add_package" class="btn nav-link mb-md-0 active">+</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-11">
                                <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="package_buttons_container">
                                </ul>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-lg-10 col-12">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        {{__('Shipping box')}}
                                    </div>
                                    <div class="col-lg-9 col-12">
                                        <div class="row">
                                            <div class="col-12 col-md-4 d-flex">
                                                <span class="text-center">{{__('Length')}}</span>
                                                <input required id="length" name="length" type="number" step="0.01" placeholder="0" class="input-no-border text-right text-md-center" size="5" />
                                            </div>
                                            <div class="col-12 col-md-4 d-flex">
                                                <span class="text-center">{{__('Width')}}</span>
                                                <input required id="width" name="width" type="number" step="0.01" placeholder="0" class="input-no-border text-right text-md-center" size="5" />
                                            </div>
                                            <div class="col-12 col-md-4 d-flex">
                                                <span class="text-center">{{__('Height')}}</span>
                                                <input required id="height" name="height" type="number" step="0.01" placeholder="0" class="input-no-border text-right text-md-center" size="5" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <select id="shipping_box" name="shipping_box" class="form-control box_select" data-toggle="select" data-placeholder="{{ __('Choose shipping box') }}">
                                            @foreach($shippingBoxes as $shippingBox)
                                                <option
                                                    value="{{$shippingBox->id}}"
                                                    data-length="{{ $shippingBox->length }}"
                                                    data-width="{{ $shippingBox->width }}"
                                                    data-height="{{ $shippingBox->height }}"
                                                    data-height-locked="{{ $shippingBox->height_locked }}"
                                                    data-length-locked="{{ $shippingBox->length_locked }}"
                                                    data-width-locked="{{ $shippingBox->width_locked }}"
                                                    @if ($shippingBox->id == customer_settings($order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_SHIPPING_BOX_ID)) selected="selected" @endif
                                                >{{$shippingBox->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 border-left">
                                <div class="row align-items-center">
                                    <div class="col-6 col-lg-12 text-right text-lg-center">
                                        {{ __('Weight') }}
                                    </div>
                                    <div class="col-6 col-lg-12">
                                        <input type="number" step="0.1" min="0.1" placeholder="0" id="weight" name="weight" class="input-no-border input-no-border-packing-weight text-left text-lg-center"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row flex-lg-grow-1 min-h-0 h-lg-100 my-2">
                            <div class="col-12 my-4 h-lg-100 overflow-auto">
                                <div id="package_container"></div>
                            </div>
                        </div>
                        <div class="row my-2 flex-lg-grow-1">
                            <div class="col-12 mt-lg-0 mt-3 d-flex justify-content-end align-items-center" id="submit_print_container">
                                @if (!$bulkShipBatch)
                                    <div class="d-flex align-items-center">
                                        <div data-target="#choosePrinter" data-toggle="modal" class="mr-2">
                                            <i  class="pr-2 picon-printer-light icon-lg align-middle"></i>
                                        </div>
                                        <a href="{{route('order.getOrderSlip', ['order'=>$order])}}" target="_blank" id="order_slip_submit" class=" font-weight-700 d-inline-block d-md-none">
                                            <i class="picon-receipt-light icon-lg rounded icon-background text-white d-block d-md-none"></i>
                                        </a>
                                    </div>
                                @else
                                    <input type="hidden" name="printer_id" value="pdf" id="input-printer_id" />
                                @endif
                                <input type="hidden" name="print_packing_slip"/>
                                <div class="btn-group">
                                    <button class="btn btn-primary confirm-ship-button" type="button" id="confirm-dropdown">
                                        {{ __('Ship Order') }}
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split opacity-8" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" data-dismiss="modal" class="dropdown-item ship-button">
                                            {{ __('Ship Order') }}
                                        </button>
                                        <button type="button" data-dismiss="modal" class="dropdown-item ship-and-print-button">
                                            {{ __('Ship and Print Order') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('shared.modals.shippingInformationEdit')
            @include('shared.modals.choosePrinter')
            @include('shared.modals.serialNumberAddModal')
        </form>
    </div>
@endsection

@push('js')
    <script>
        new PackingSingleOrder(@json($order->id), @json($order->packing_note));
    </script>
@endpush


