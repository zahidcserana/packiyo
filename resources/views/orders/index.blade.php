@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Orders',
        'subtitle' => 'Manage Orders',
        'button' => [
            'title' => __('Create Order'),
            'href' => route('order.create'),
        ]
    ])

    <x-datatable
            search-placeholder="{{ __('Search order') }}"
            table-id="orders-table"
            filters="local"
            filter-menu="shared.collapse.forms.orders"
            :data="$data"
            datatableOrder="{!! json_encode($datatableOrder) !!}"
            :bulk-edit=true
            bulk-edit-form="orders.bulk_edit"
            count-records-url="{{ route('order.countRecords') }}"
            :show-total-records=true
            disable-autoload="{{ (bool) customer_settings(app('user')->getSessionCustomer()->id ?? null, \App\Models\CustomerSetting::CUSTOMER_SETTING_DISABLE_AUTOLOAD_ORDERS, 0) }}"
    >
        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Import CSV') }}" data-toggle="modal"
                   data-target="#import-orders-modal">
                    <i class="picon-upload-light icon-lg"></i>
                </a>
            </div>
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export CSV') }}" data-toggle="modal"
                   data-target="#export-orders-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @include('shared.modals.orderView')
    @include('shared.modals.components.orders.importCsv')
    @include('shared.modals.components.orders.exportCsv')
@endsection
@push('js')
    <script>
        new Order('{{$keyword}}');
    </script>
@endpush
