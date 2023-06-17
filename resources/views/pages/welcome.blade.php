@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    <div class="header bg-primary py-5 pb-7 pt-lg-9">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-9 pt-5">
                        <h1 class="text-white">{{ __('Welcome') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt--10 pb-5"></div>
@endsection
