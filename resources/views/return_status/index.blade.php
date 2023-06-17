@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Returns',
        'subtitle' => 'Statuses',
        'button' => [
            'title' => __('Add Return status'),
            'href' => route('return_status.create'),
            'data-toggle' => 'modal',
            'data-target' => '#create-edit-modal',
        ]
    ])

    <x-datatable
        search-placeholder="Search statuses"
        table-id="return-status-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
    />

    <div class="modal fade confirm-dialog" id="create-edit-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        </div>
    </div>
@endsection

@push('js')
    <script>
        new ReturnStatus()
    </script>
@endpush

