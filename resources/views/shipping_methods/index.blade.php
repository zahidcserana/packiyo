@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Settings',
        'subtitle' => 'Shipping Method',
    ])

    <x-datatable
        search-placeholder="{{ __('Search methods') }}"
        table-id="shipping-method-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
    />
@endsection

@push('js')
    <script>
        new ShippingMethod()
    </script>
@endpush
