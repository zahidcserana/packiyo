
@extends('layouts.app', ['title' => __('Customer Management')])

@section('content')
    @include('layouts.headers.auth', [
            'title' => 'Customers',
            'subtitle' =>  __('Edit customer')
        ])
    <div class="container-fluid bg-lightGrey select2Container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('customer.index') }}" class="text-black font-sm font-weight-600 d-inline-flex align-items-center bg-white border-8 px-3 py-2 mt-3">
                <i class="picon-arrow-backward-filled icon-lg icon-black mr-1"></i>
                {{ __('Back') }}
            </a>
        </div>
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-body">
                        <div class="nav-wrapper">
                            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-1-tab"
                                       aria-controls="tabs-icons-text-1" aria-selected="false" href="{{ route('customer.edit', [ 'customer' => $customer ]) }}"><i class="ni ni-cloud-upload-96 mr-2"></i>{{ __('Customer') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab"
                                       aria-controls="tabs-icons-text-2" aria-selected="false" href="{{ route('customer.editUsers', [ 'customer' => $customer ]) }}"><i class="ni ni-bell-55 mr-2"></i>{{ __('Users') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" href="{{ route('customers.easypost_credentials.index', [ 'customer' => $customer ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>{{__('Easypost Credentials')}}</a>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-3-tab" aria-controls="tabs-icons-text-3" aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>{{__('Webshipper Credentials')}}</div>
                                </li>
                            </ul>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="table-responsive table-overflow items-table mb-4">
                                    <table class="col-12 table align-items-center table-flush">
                                        <thead>
                                        <tr>
                                            <th scope="col">{{ __('API Base URL') }}</th>
                                            <th scope="col">{{ __('API Key') }}</th>
                                            <th scope="col">{{ __('Order channel ID') }}</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="item_container">
                                        @foreach($customer->webshipperCredentials as $webshipperCredential)
                                            <tr>
                                                <td>
                                                    <a href="{{ $webshipperCredential->api_base_url }}">
                                                        {{ $webshipperCredential->api_base_url  }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {!! $webshipperCredential->api_key !!}
                                                </td>
                                                <td>
                                                    {{ $webshipperCredential->order_channel_id  }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('customers.webshipper_credentials.edit', ['customer' => $customer, 'webshipper_credential' => $webshipperCredential]) }}" class="table-icon-button">
                                                        <i class="picon-edit-filled icon-orange icon-lg" title="{{ __('Edit') }}"></i>
                                                    </a>
                                                    <form action="{{ route('customers.webshipper_credentials.destroy', ['customer' => $customer, 'webshipper_credential' => $webshipperCredential]) }}" method="post" class="d-inline-block">
                                                        <input type="hidden" name="_method" value="delete">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="id" value="{{ $webshipperCredential->id }}">
                                                        <button type="button" class="table-icon-button" data-confirm-message="{{ __('Are you sure you want to delete this credential') }}" data-confirm-button-text="{{ __('Delete') }}">
                                                            <i class="picon-trash-filled icon-orange del_icon icon-lg" title="{{ __('Delete') }}"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('customers.webshipper_credentials.create', compact('customer')) }}" class="btn bg-logoOrange text-white my-2 px-3 py-2 font-weight-700 border-8">{{ __('Add new credentials') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

