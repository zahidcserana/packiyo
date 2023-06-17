@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Account'),
        'subtitle' => __('Edit billing details')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <a href="{{ url()->previous() }}" class="text-black font-sm font-weight-600 d-inline-flex align-items-center bg-white border-8 px-3 py-2 mt-3">
                        <i class="picon-arrow-backward-filled icon-lg icon-black mr-1"></i>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid ">
        <form action="{{ route('payment.updateBillingDetails', compact('customer')) }}" method="post"
              class="card px-3 py-4 border-8" id="store-payment-method">
            @csrf
            <h2>{{ __('Billing details') }}</h2>
            <div class="d-flex orderContactInfo flex-column">
                <div class="d-lg-flex">
                    @include('shared.forms.input', [
                        'name' => 'account_holder_name',
                        'containerClass' => 'w-50',
                        'label' => __('Account holder name'),
                        'value' => $billingDetails->account_holder_name ?? '',
                        'required' => 'required'
                    ])
                    @include('shared.forms.input', [
                      'name' => 'email',
                      'containerClass' => 'w-50',
                      'label' => __('Email'),
                      'value' => $billingDetails->email ?? ''
                   ])
                </div>
                <div class="d-lg-flex">
                    @include('shared.forms.input', [
                      'name' => 'address',
                      'containerClass' => 'w-50',
                      'label' => __('Address'),
                      'value' => $billingDetails->address ?? '',
                      'required' => 'required'
                    ])
                    @include('shared.forms.input', [
                        'name' => 'address2',
                        'containerClass' => 'w-50',
                        'label' => __('Address 2'),
                        'value' => $billingDetails->address2 ?? ''
                    ])
                </div>
                <div class="d-lg-flex">
                    @include('shared.forms.input', [
                        'name' => 'city',
                        'containerClass' => 'w-50',
                        'label' => __('City'),
                        'value' => $billingDetails->city ?? ''
                    ])
                    @include('shared.forms.ajaxSelect', [
                        'label' => __('Country'),
                        'name' => 'country_id',
                        'containerClass' => 'w-50 form-group mb-0 mx-2 text-left',
                        'placeholder' => __('Select country'),
                        'url' => route('site.filterCountries'),
                        'minInputLength' => 0,
                        'default' => [
                            'id' => $billingDetails->country_id ?? '',
                            'text' => $billingDetails->country->name ?? ''
                        ]
                    ])
                </div>
                <div class="d-lg-flex">
                    @include('shared.forms.input', [
                        'name' => 'postal_code',
                        'containerClass' => 'w-50',
                        'label' => __('Postal code'),
                        'value' => $billingDetails->postal_code ?? '',
                        'required' => 'required'
                    ])
                    @include('shared.forms.input', [
                        'name' => 'state',
                        'containerClass' => 'w-50',
                        'label' => __('State'),
                        'type' => 'text',
                        'value' => $billingDetails->state ?? ''
                    ])
                </div>
                <div class="d-lg-flex">
                    @include('shared.forms.input', [
                        'name' => 'phone',
                        'containerClass' => 'w-50',
                        'label' => __('Phone number'),
                        'type' => 'text',
                        'value' => $billingDetails->phone ?? ''
                    ])
                </div>
            </div>
            <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">
                {{ __('Save') }}
            </button>
        </form>
    </div>
@endsection
