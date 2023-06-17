@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => __('Print jobs'),
        'subtitle' => __(''),
    ])

    <x-datatable
        search-placeholder="{{ __('Search print jobs') }}"
        table-id="jobs-table"
        filters="local"
        filter-menu="shared.collapse.forms.print_jobs"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
    />

@endsection
@push('js')
    <script>
        new PrintJob('{{ $printer->id }}');
    </script>
@endpush
