@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Inventory',
        'subtitle' => 'Product Locations',
    ])

    <x-datatable
        search-placeholder="{{ __('Search product locations') }}"
        table-id="product-location-table"
        filters="local"
        filter-menu="shared.collapse.forms.product_locations"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
    >
        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Import Products') }}" data-toggle="modal" data-target="#import-inventory-modal">
                    <i class="picon-upload-light icon-lg"></i>
                </a>
            </div>
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export Products') }}" data-toggle="modal" data-target="#export-inventory-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @include('shared.modals.components.location.importInventoryCsv')
    @include('shared.modals.components.location.exportInventoryCsv')
@endsection

@push('js')
    <script>
        new ProductLocation()
    </script>

@endpush
