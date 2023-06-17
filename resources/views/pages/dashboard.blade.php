@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth')

    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <h1>Dashboard</h1>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Dashboard body</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

