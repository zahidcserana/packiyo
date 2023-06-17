@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Purchase Orders',
        'subtitle' => 'Manage Vendors',
    ])

    <x-datatable
        searchPlaceholder="{{ __('Search vendor') }}"
        tableId="supplier-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />

    @include('shared.modals.vendorModals')
@endsection

@push('js')
    <script>
        new Supplier()
    </script>
@endpush
