@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Inventory'),
        'subtitle' => __('Change Log')
    ])
    @endcomponent

    <x-datatable
        search-placeholder="Search logs"
        table-id="inventory-log-table"
        filters="local"
        filter-menu="shared.collapse.forms.inventory_log"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        disable-autoload="{{ (bool) customer_settings(app('user')->getSessionCustomer()->id ?? null, \App\Models\CustomerSetting::CUSTOMER_SETTING_DISABLE_AUTOLOAD_INVENTORY_CHANGE_LOG, 0) }}"
    >
        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export Inventory') }}" data-toggle="modal" data-target="#export-inventory-log-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @include('shared.modals.components.inventory_logs.exportInventory')
@endsection

@push('js')
    <script>
        new InventoryLog();
    </script>
@endpush
