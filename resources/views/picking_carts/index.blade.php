@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Shipments',
        'subtitle' => 'Picking Carts',
        'button' => [
            'title' => __('Add picking cart'),
            'href' => route('picking_carts.create')
        ]
    ])

    <x-datatable
        search-placeholder="{{ __('Search picking carts') }}"
        table-id="picking-cart-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
    />
@endsection

@push('js')
    <script>
        new PickingCart()
    </script>
@endpush
