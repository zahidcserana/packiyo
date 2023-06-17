@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Webshipper credentials'),
        'subtitle' => __('Update')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('customers.webshipper_credentials.index', compact('customer')) }}" class="text-black font-sm font-weight-600 d-inline-flex align-items-center bg-white border-8 px-3 py-2 mt-3">
                <i class="picon-arrow-backward-filled icon-lg icon-black mr-1"></i>
                {{ __('Back') }}
            </a>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('customers.webshipper_credentials.update', ['customer' => $customer, 'webshipper_credential' => $webshipperCredential]) }}" id="create-webshipper-credentials-form" autocomplete="off">
                            @csrf
                            {{ method_field('PUT') }}
                            <div class="pl-lg-4">
                                @if(!isset($customer))
                                    <div class="searchSelect">
                                        @include('shared.forms.new.ajaxSelect', [
                                        'url' => route('user.getCustomers'),
                                        'name' => 'customer_id',
                                        'className' => 'ajax-user-input customer_id',
                                        'placeholder' => __('Select customer'),
                                        'label' => __('Customer'),
                                        'default' => [
                                            'id' => old('customer_id'),
                                            'text' => ''
                                        ],
                                        'fixRouteAfter' => '.ajax-user-input.customer_id'
                                    ])
                                    </div>
                                @else
                                    @if(isset($customer))
                                        <input type="hidden" name="customer_id" value="{{ $customer->id }}" class="customer_id" />
                                    @elseif(isset($sessionCustomer))
                                        <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
                                    @endif
                                @endif
                                @include('shared.forms.input', [
                                   'name' => 'api_base_url',
                                   'label' => __('API base URL'),
                                   'value' => $webshipperCredential->api_base_url,
                                   'required' => true
                                ])
                                @include('shared.forms.input', [
                                   'name' => 'api_key',
                                   'label' => __('API key'),
                                   'value' => $webshipperCredential->api_key,
                                   'required' => true
                                ])
                                @include('shared.forms.input', [
                                  'name' => 'order_channel_id',
                                  'label' => __('Order channel ID'),
                                  'value' => $webshipperCredential->order_channel_id,
                                  'type' => 'number',
                                  'required' => true
                                ])
                                <div class="text-center">
                                    <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
