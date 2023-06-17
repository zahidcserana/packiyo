@extends('layouts.app')

@section('content')
    @if($customer->count() > 1 || ($customer->count() === 1  && $customer->first()->isParent()))
        @include('layouts.headers.auth', [
            'title' => 'Inventory',
            'subtitle' => 'Locations',
            'button' => [
                'title' => __('Add location'),
                'href' => '#',
                'data-toggle' => 'modal',
                'data-target' => '#locationCreateModal',
            ]
        ])
    @else
        @include('layouts.headers.auth', [
                'title' => 'Inventory',
                'subtitle' => 'Locations',
            ])
    @endif

    <x-datatable
        search-placeholder="{{ __('Search locations') }}"
        table-id="locations-table"
        filters="local"
        filter-menu="shared.collapse.forms.locations"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}">

        <x-slot name="tableActions">
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Import Locations') }}" data-toggle="modal"
                   data-target="#import-locations-modal">
                    <i class="picon-upload-light icon-lg"></i>
                </a>
            </div>
            <div class="mr-0 px-2">
                <a href="#" title="{{ __('Export Locations') }}" data-toggle="modal"
                   data-target="#export-locations-modal">
                    <i class="picon-archive-light icon-lg"></i>
                </a>
            </div>
        </x-slot>
    </x-datatable>

    @if($customer->count() > 1 || ($customer->count() === 1  && $customer->first()->isParent()))
        @include('shared.modals.locationModals')
    @endif

    @include('shared.modals.components.location.importCsv')
    @include('shared.modals.components.location.exportCsv')
@endsection

@push('js')
    <script>
        new LocationForm()
    </script>
@endpush
