@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Purchase Orders',
        'subtitle' => 'Manage Purchase Orders',
        'button' => [
            'title' => __('Create PO'),
            'href' => route('purchase_orders.create'),
        ]
    ])

    <x-datatable
            searchPlaceholder="{{ __('Search purchase order') }}"
            tableId="purchase-orders-table"
            filters="local"
            filter-menu="shared.collapse.forms.purchase_orders"
            :data="$data"
            datatableOrder="{!! json_encode($datatableOrder) !!}"
            bulkEdit=true
    >
        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Import CSV') }}" data-toggle="modal"
                   data-target="#import-purchase-orders-modal">
                    <i class="picon-upload-light icon-lg"></i>
                </a>
            </div>
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export CSV') }}" data-toggle="modal"
                   data-target="#export-purchase-orders-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @include('shared.modals.components.purchase_orders.importCsv')
    @include('shared.modals.components.purchase_orders.exportCsv')
@endsection

@push('js')
    <script>
        new PurchaseOrder('{{$keyword}}');
    </script>
@endpush
