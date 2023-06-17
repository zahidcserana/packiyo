@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Returns',
        'subtitle' => 'All returns',
        'button' => [
            'title' => __('Add Return'),
            'href' =>  route('return.create'),
        ]
    ])

    <x-datatable
        search-placeholder="Search returns"
        table-id="returns-table"
        filters="local"
        filter-menu="shared.collapse.forms.returns"
        :data="$data"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />

    <div class="modal fade confirm-dialog" id="return-show" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        </div>
    </div>

    <div class="modal fade confirm-dialog" id="return-status" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        </div>
    </div>
@endsection

@push('js')
    <script>
        new ReturnOrder('{{$keyword}}')
    </script>
@endpush
