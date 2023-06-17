<div class="modal-content" id="orderViewModal">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Order Details') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-md-row" id="" role="tablist">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active" id="order-details-information-tab"
                       data-toggle="tab"
                       href="#order-details-information-tab-content" role="tab"
                       aria-controls="tabs-icons-text-1"
                       aria-selected="true">
                        {{ __('General Information') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="order-details-items-tab"
                       data-toggle="tab"
                       href="#order-details-items-tab-content" role="tab"
                       aria-controls="tabs-icons-text-3" aria-selected="false">
                        {{ __('Order Items') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="order-details-log-tab"
                       data-toggle="tab"
                       href="#order-details-log-tab-content" role="tab"
                       aria-controls="tabs-icons-text-3" aria-selected="false">
                        {{ __('Order Log') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0"
                       target="_blank"
                       href="{{ route('order.getOrderSlip', $order) }}" role="tab"
                       aria-controls="tabs-icons-text-3" aria-selected="false">
                        {{ __('Order Slip') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content text-black bg-formWhite border-12 p-3 inputs-container">
            <div class="tab-pane fade show active" id="order-details-information-tab-content" role="tabpanel"
                 aria-labelledby="order-details-information-tab">
                <div class="d-lg-flex justify-content-between">
                    <div class="w-50">
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Number') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->number ?? "-" }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Tax') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ ($order->tax ?? "0.00") . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Shipping Carrier') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->shipment ?? "-" }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Address Hold)') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->address_hold ? __("Yes") : __("No") }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Ship At Date') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->required_shipping_date_at ?? "-" }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Shipping Price') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ ($order->shipping?->price ?? "0.00") . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Notes') }}</span>
                            <span class="text-black ml-2 text-left font-sm font-weight-600">{!! $order->gift_note ?? "-" !!}</span>
                        </div>
                    </div>
                    <div class="w-50">
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Shipment Type') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->shipping?->type ?? "-" }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Payment Hold') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->payment_hold ? __("Yes") : __("No") }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Priority') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->priority ?? __("No") }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Shipping Method') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->shipping?->method ?? "-" }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Fraud Hold') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->fraud_hold ? __("Yes") : __("No") }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-lg-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Operator Hold') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->operator_hold ? __("Yes") : __("No") }}</span>
                        </div>
                        <div class="d-flex justify-content-between ml-3 border-bottom py-3">
                            <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Allow partial') }}</span>
                            <span class="text-black text-right font-sm font-weight-600">{{ $order->allow_partial ? __("Yes") : __("No") }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="order-details-items-tab-content" role="tabpanel"
                 aria-labelledby="order-details-items-tab">
                <div class="w-100">
                    @if (! empty($order->orderItems) && count($order->orderItems))
                        <table class="table col-12 text-left no-footer">
                            <thead>
                            <tr>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Name') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Quantity') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Pending') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Allocated') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Shipped') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Sku') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td class="py-4 text-black font-weight-600 font-sm"><a href="{{ route('product.edit', $item->product) }}" target="_blank">{{ $item->name ?? "-" }}</a></td>
                                    <td class="py-4 text-black font-weight-600 font-sm">{{ $item->quantity ?? "-" }}</td>
                                    <td class="py-4 text-black font-weight-600 font-sm">{{ $item->quantity_pending ?? "-" }}</td>
                                    <td class="py-4 text-black font-weight-600 font-sm">{{ $item->quantity_allocated  ?? "-" }}</td>
                                    <td class="py-4 text-black font-weight-600 font-sm">{{ $item->quantity_shipped ?? "-" }}</td>
                                    <td class="py-4 text-black font-weight-600 font-sm">{{ $item->sku ?? "-" }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="w-100">
                            <div class="my-5 pb-4">
                                <h6 class="modal-title text-logoOrange text-center">{{ __('No Related Items') }}</h6>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="tab-pane fade" id="order-details-log-tab-content" role="tabpanel"
                 aria-labelledby="order-details-log-tab">
                <div class="w-100">
                        <table class="table col-12 text-left no-footer">
                            <thead>
                            <tr>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Date') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('User') }}</th>
                                <th class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Note') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="py-4 text-black font-weight-600 font-sm">{{ $order->created_at }}</td>
                                <td class="py-4 text-neutral-text-gray font-weight-600 font-sm">{{ $order->customer->contactInformation->name }} </td>
                                <td class="py-4 text-black font-weight-600 font-sm">{{ __('Order Created') }}</td>
                            </tr>
                            @if (! empty($order->revisionHistory) && count($order->revisionHistory))
                                @foreach($order->revisionHistory as $history)
                                    @if($history->key === 'tag')
                                        <tr>
                                            <td class="py-4 text-black font-weight-600 font-sm">{{ $history->updated_at }}</td>
                                            <td class="py-4 text-neutral-text-gray font-weight-600 font-sm">{{ $history->userResponsible()->contactInformation->name ?? '' }} </td>
                                            <td class="py-4 text-black font-weight-600 font-sm">{!! __('Added the following tags: ') . '<em>'. $history->newValue() . '</em>'   !!}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="py-4 text-black font-weight-600 font-sm">{{ $history->updated_at }}</td>
                                            <td class="py-4 text-neutral-text-gray font-weight-600 font-sm">{{ $history->userResponsible()->contactInformation->name ?? '' }} </td>
                                            <td class="py-4 text-black font-weight-600 font-sm">{!! __('Changed') . ' <em>' .$history->fieldName() . '</em>' . ' ' . __('from') . ' <em>' . ($history->oldValue() ?? __('null')) . '</em>' . ' ' . __('to') . ' <em>' . $history->newValue() . '</em>'   !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
    @if (!request()->get('disableEdit'))
    <div class="modal-footer d-flex justify-content-center">
        <a href="{{ route('order.edit', ['order' => $order->id]) }}">
            <button type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700" id="editProduct">
                {{ __('Edit') }}
            </button>
        </a>
    </div>
    @endif
</div>
