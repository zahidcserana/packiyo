@extends('layouts.app')

@section('content')
    <div class="container-fluid half-bg-lightGrey flex-grow-1 login">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-lg-6 col-md-12 px-lg-3 bg-lightGrey">
                <div class="">
                    <img class="w-100" src="{{ asset('img/login-background.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-md-12 ">
                <div class="pl-0 px-xl-9 px-lg-3 w-100">
                    <h2 class="mt-3 text-center text-logo-orange font-header-lg font-weight-600 mb-5">{{ __('Sign In') }}</h2>
                    <div>
                        <form role="form" class="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <input type="hidden" name="timezone" value="">
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" type="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                           <div class="d-flex justify-content-between">
                               <div class="custom-control custom-control-alternative custom-checkbox font-weight-600">
                                   <input class="custom-control-input" name="remember" id="customCheckLogin" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                   <label class="custom-control-label" for="customCheckLogin">
                                       <span class="text-black font-xs">{{ __('Remember me') }}</span>
                                   </label>
                               </div>
                               <div class="text-center font-weight-600 font-sm">
                                   @if (Route::has('password.request'))
                                       <a href="{{ route('password.request') }}" class="text-black text-underline">
                                           <span>{{ __('Forgot password?') }}</span>
                                       </a>
                                   @endif
                               </div>
                           </div>
                            <div class="text-center formSubmit">
                                <button type="submit" class="btn bg-logoOrange text-white w-100 my-4 font-weight-700">{{ __('Sign in') }}</button>
                            </div>
                            <div class="text-center font-weight-600 font-sm text-black mb-4">
                                <span>{{ __("Don't have an account yet?") }}</span><a href="" class="text-logo-orange text-underline">{{ __('Sign Up') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
