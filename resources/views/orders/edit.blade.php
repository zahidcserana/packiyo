@extends('layouts.app')

@section('content')
@component('layouts.headers.auth', [ 'title' => __('Orders'), 'subtitle' => 'Manage Orders'])
@endcomponent
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-lg-flex align-items-center justify-content-between mb-4">
                <a href="{{ route('order.index') }}" class="text-black font-sm font-weight-600 d-inline-flex align-items-center bg-white border-8 px-3 py-2 mt-3">
                    <i class="picon-arrow-backward-filled icon-lg icon-black mr-1"></i>
                    {{ __('Back') }}
                </a>
                <div class="d-lg-flex align-items-center justify-content-between">
                    @if($order->getStatusText() === \App\Models\Order::STATUS_FULFILLED)
                        <p class="d-inline-flex align-items-center mt-3 ml-lg-3 mb-0 font-weight-600 text-black">{{ __(\App\Models\Order::STATUS_FULFILLED) }}</p>
                    @elseif($order->getStatusText() === \App\Models\Order::STATUS_CANCELLED)
                        <p class="d-inline-flex align-items-center mt-3 ml-lg-3 mb-0 font-weight-600 text-black">{{ __(\App\Models\Order::STATUS_CANCELLED) }}</p>
                    @else
                        <a class="d-inline-flex align-items-center mt-3 ml-lg-3" href="#">
                            <button
                                data-cancel-action="{{ route('order.cancel', ['order' => $order->id]) }}"
                                data-order-number="{{ $order->number }}"
                                data-cancellation-message="{{ $partiallyShipped ? __('Order is already partially fulfilled. Do you want to cancel the remaining of the order?') : __('Are you sure you want to cancel this order?') }}"
                                type="button" class="btn bg-logoOrange text-white mx-auto px-3 py-2 font-weight-700 border-8">{{ __('Cancel Order') }}</button>
                        </a>
                        <a class="d-inline-flex align-items-center mt-3 ml-lg-3" href="#">
                            <button
                                data-fulfill-action="{{ route('order.fulfill', ['order' => $order->id]) }}"
                                data-order-number="{{ $order->number }}"
                                data-mark-as-fulfilled-message="{{ __('Are you sure you want to mark this order as fulfilled?') }}"
                                type="button" class="btn bg-logoOrange text-white mx-auto px-3 py-2 font-weight-700 border-8">{{ __('Mark as fulfilled') }}</button>
                        </a>
                    @endif
                    @if(count($order->shipments) > 0)
                        <a class="d-inline-flex align-items-center mt-3 ml-lg-3 font-weight-700" href="#">
                            <button
                                data-target="#order-reship-modal" data-toggle="modal"
                                type="button" class="btn bg-logoOrange text-white mx-auto px-3 py-2 font-weight-700 border-8">{{ __('Re-Ship') }}
                            </button>
                        </a>
                    @endif
                    @if(!$order->isEmptyOrderItemQuantityShipped() && ($order->getStatusText() === \App\Models\Order::STATUS_PENDING || $order->fulfilled_at) && count(app('order')->getShippedOrderItems($order)) > 0)
                        <a class="d-inline-flex align-items-center mt-3 ml-3 font-weight-700" href="#">
                            <button
                                data-target="#order-return-modal" data-toggle="modal"
                                type="button" class="btn bg-logoOrange text-white mx-auto px-3 py-2 font-weight-700 border-8">{{ __('Return Order') }}
                            </button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-forms.base-ajax
        action="{{ route('order.update', $order) }}"
        success-redirect="{{ route('order.index') }}"
        method="PUT"
        dataid="{{ $order->id }}"
        :editable="true"
    >
        <div class="col-lg-8 col-12">
            <x-forms.section title="{{ __('Order Items') }}" class="">
                <div class="col-12">
                    @include('orders.sections._items')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Notes') }}" class="">
                <div class="col-12 textarea-section">
                    @include('orders.sections._notes')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Order Totes') }}" class="">
                <div class="col-12">
                    @include('orders.sections._totes')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Order Log') }}" class="">
                <div class="col-12" id="audit-log-container" audit-url="{{ route('order.audits', ['order' => $order]) }}">
                    @include('components.audit')
                </div>
            </x-forms.section>
        </div>
        <div class="col-lg-4 col-12">
            <x-forms.section title="{{ __('Order Details') }}" class="" :autoSave="true">
                <div class="col-12">
                    @include('orders.sections._details')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Order Settings') }}" class="" :autoSave="true">
                <div class="col-12">
                    @include('orders.sections._settings')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Shipping method') }}" class="" :autoSave="true">
                <div class="col-12">
                    @include('orders.sections._shipping_method')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Shipments') }}" class="" :autoSave="true">
                <div class="col-12">
                    @include('orders.sections._shipments')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Tags') }}" class="" :autoSave="true">
                <div class="col-12">
                    @include('orders.sections._tags')
                </div>
            </x-forms.section>
            <x-forms.section title="{{ __('Customer') }}" class="d-none">
                <div class="col-12">
                    @include('shared.forms.dropdowns.customer_selection', [
                        'route' => route('order_status.filterCustomers'),
                        'readonly' => isset($order->customer->id) ? 'true' : null,
                        'id' => $order->customer->id ?? old('customer_id'),
                        'containerClass' => 'mx-0',
                        'label' => '',
                        'text' => $order->customer->contactInformation->name ?? ''
                    ])
                </div>
            </x-forms.section>
        </div>
    </x-forms.base-ajax>
</div>

@include('shared.modals.orderKit')
@include('shared.modals.dynamicKits')
@include('shared.modals.components.returns.order_reship')
@include('shared.modals.components.returns.order_return', ['dataKeyboard' => false])

@endsection

@push('js')
    <script>
        new Order();
    </script>
@endpush
