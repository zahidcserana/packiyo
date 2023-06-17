@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => __('Printers'),
        'subtitle' => __(''),
    ])

    <x-datatable
        search-placeholder="{{ __('Search printers') }}"
        table-id="printers-table"
        filters="local"
        filter-menu="shared.collapse.forms.printers"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
    />
@endsection
@push('js')
    <script>
         new Printer();
    </script>
@endpush
