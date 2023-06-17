@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Inventory',
        'subtitle' => 'Products',
        'button' => [
            'title' => __('Create Product'),
            'href' => '#',
            'data-toggle' => 'modal',
            'data-target' => '#productCreateModal',
        ]
    ])

    <x-datatable
            search-placeholder="{{ __('Search product') }}"
            table-id="products-table"
            filters="local"
            filter-menu="shared.collapse.forms.products"
            :data="$data"
            datatableOrder="{!! json_encode($datatableOrder) !!}"
            :bulk-edit=true
            bulk-edit-form="products.bulk_edit"
            disable-autoload="{{ (bool) customer_settings(app('user')->getSessionCustomer()->id ?? null, \App\Models\CustomerSetting::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS, 0) }}"
    >
        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Import Products') }}" data-toggle="modal"
                   data-target="#import-products-modal">
                    <i class="picon-upload-light icon-lg"></i>
                </a>
            </div>
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export Products') }}" data-toggle="modal"
                   data-target="#export-products-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @include('shared.modals.productCreate')
    @include('shared.modals.components.product.importCsv')
    @include('shared.modals.components.product.exportCsv')
    @include('shared.modals.components.product.recover')
    @include('shared.modals.components.product.image')
@endsection

@push('js')
    <script>
        new Product('{{$keyword}}');
    </script>
@endpush
