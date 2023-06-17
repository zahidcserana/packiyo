@extends('layouts.location_layout')

@section('content')
    <div class="row">
        @foreach($customers as $customer)
            <div class="col py-2 border">
                <a href="{{ route('location_layout.warehouse.index', ['customer' => $customer]) }}">{{ $customer->contactInformation->name }}</a>
            </div>
        @endforeach
    </div>
@endsection
