@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('User'),
        'subtitle' => __('Activity Log')
    ])
    @endcomponent

    <x-datatable
        search-placeholder="{{ __('Search log') }}"
        table-id="activity-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
    />
@endsection

@push('js')
    <script>
        new Activity()
    </script>
@endpush
