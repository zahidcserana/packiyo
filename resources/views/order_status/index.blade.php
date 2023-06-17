@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Order',
        'subtitle' => 'Statuses',
        'button' => [
            'title' => __('Add order status'),
            'href' => route('order_status.create')
        ]
    ])

    <x-datatable
        search-placeholder="{{ __('Search statuses') }}"
        table-id="order-status-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />
@endsection

@push('js')
    <script>
        new OrderStatus()
    </script>
@endpush

