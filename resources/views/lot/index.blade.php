@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Lots',
        'subtitle' => '',
        'button' => [
            'title' => __('Add Lot'),
            'href' => route('lot.create'),
            'data-toggle' => 'modal',
            'data-target' => '#create-edit-modal',
        ]
    ])

    <x-datatable
        search-placeholder="Search lots"
        table-id="lot-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />

    @include('shared.modals.lotModals')
@endsection

@push('js')
    <script>
        new Lot()
    </script>
@endpush

