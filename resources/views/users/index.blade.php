@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Settings',
        'subtitle' => 'Manage Users',
        'button' => [
            'title' => __('Add User'),
            'href' => route('user.create')
        ]
    ])

    <x-datatable
        search-placeholder="Search Users"
        table-id="users-table"
        datatableOrder="{!! json_encode($datatableOrder) !!}"
        bulkEdit=true
    />
@endsection

@push('js')
    <script>
     Echo.channel('prints')
        .listen('NewPrint', (e) => {
            console.log(e.print);
        })

        new User()
    </script>
@endpush

