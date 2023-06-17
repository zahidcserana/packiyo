@extends('layouts.location_layout')

@section('content')
    <div class="row">
        @foreach($warehouses as $warehouse)
            <div class="col py-2 border">
                <a href="{{ route('location_layout.location.index', ['warehouse' => $warehouse]) }}">{{ $warehouse->contactInformation->name }}</a>
            </div>
        @endforeach
    </div>
@endsection
