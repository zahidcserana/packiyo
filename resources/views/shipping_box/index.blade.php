@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => __('Shipments'),
        'subtitle' => __('Boxes'),
        'button' => [
            'title' => __('Add shipping box'),
            'href' => route('shipping_box.create')
        ],
    ])

    <x-datatable
        search-placeholder="{{ __('Search boxes') }}"
        table-id="shipping-box-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
    />
@endsection

@push('js')
    <script>
        new ShippingBox()
    </script>
@endpush

