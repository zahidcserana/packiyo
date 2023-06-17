@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    <div class="container-fluid half-bg-lightGrey flex-grow-1 d-flex">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 px-lg-3 bg-lightGrey">
                <div>
                    <img class="w-100" src="{{ asset('img/login-background.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-md-12 d-flex flex-column">
                <div class="pl-0 py-4 px-xl-7 px-lg-3 w-100">
                    <a href="{{ route('login') }}" class="text-textGray d-flex"><img src="{{ asset('img/arrow-left.svg') }}" alt=""> {{ __('Back to login') }}</a>
                </div>
                <div class="pl-0 px-xl-7 px-lg-3 w-100 d-flex flex-column flex-grow-1 justify-content-center">
                    <h2 class="text-logo-orange font-header-lg font-weight-600 mb-3">{{ __('Reset password') }}</h2>
                    <div>
                        <form role="form" class="loginForm" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="text-center formSubmit">
                                <button type="submit" class="btn bg-logoOrange text-white w-100 my-4 font-weight-700">{{ __('Send Password Reset Link') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
