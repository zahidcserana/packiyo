@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => __('Picking Batch Items')
    ])

    <div id="pickingBatchId" data-picking-batch="{{ $pickingBatch->id }}">

    <x-datatable
        search-placeholder="{{ __('Search picking batch items') }}"
        table-id="picking-batch-items-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        table-class="table-hover"
        :tags=true
    />
@endsection

@push('js')
    <script>
        new PickingBatch()
    </script>
@endpush

