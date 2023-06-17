@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Settings',
        'subtitle' => 'Location Types',
        'button' => [
            'title' => __('Add location type'),
            'href' => route('location_type.create')
        ]
    ])

    <x-datatable
        search-placeholder="{{ __('Search types') }}"
        table-id="location-type-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
        filters="local"
        filter-menu="shared.collapse.forms.locations"
        bulkEdit=true
    />
@endsection

@push('js')
    <script>
        new LocationType()
    </script>
@endpush

