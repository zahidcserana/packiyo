@extends('layouts.app')

@section('content')
    @if($customer->count() > 1 || ($customer->count() === 1  && $customer->first()->isParent()))
        @include('layouts.headers.auth', [
            'title' => 'Inventory',
            'subtitle' => 'Warehouses',
            'button' => [
                'title' => __('Add warehouse'),
                'href' => '#',
                'data-toggle' => 'modal',
                'data-target' => '#warehouseCreateModal',
            ]
        ])
    @else
        @include('layouts.headers.auth', [
            'title' => 'Inventory',
            'subtitle' => 'Warehouses'
        ])
    @endif

    <x-datatable
        search-placeholder="{{ __('Search warehouse') }}"
        table-id="warehouses-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />

    @if($customer->count() > 1 || ($customer->count() === 1  && $customer->first()->isParent()))
        @include('shared.modals.warehouseModals')
    @endif
@endsection

@push('js')
    <script>
        new Warehouse()
    </script>
@endpush
