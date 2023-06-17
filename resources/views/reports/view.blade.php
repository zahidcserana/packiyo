@extends('layouts.app')

@section('content')

    <div class="w-100">
        @include('layouts.headers.auth', [
            'title' => $reportTitle,
        ])

        <x-datatable
            search-placeholder="{{ __('Search') }}"
            table-id="{{ $reportId }}-table"
            filters="local"
            filter-menu="shared.collapse.forms.{{ $reportId }}"
            :data="$data"
            datatableOrder="{!! json_encode($datatableOrder) !!}"
            disable-autoload="{{ (bool) customer_settings(app('user')->getSessionCustomer()->id ?? null, 'disable_autoload_' . $reportId . '_report', 0) }}"
            widget-url="{{ $widgetsUrl }}"
        >
            <x-slot name="tableActions">
                <div class="mr-0 px-2">
                    <a href="#" title="{{ __('Export Report') }}" data-toggle="modal" data-target="#export-{{ $reportId }}-modal">
                        <i class="picon-archive-light icon-lg"></i>
                    </a>
                </div>
            </x-slot>
        </x-datatable>
    </div>

    @include('shared.modals.components.reports.export')

@endsection
@push('js')
    <script>
        new {{ \Illuminate\Support\Str::studly($reportId) }}Report();
    </script>
@endpush
