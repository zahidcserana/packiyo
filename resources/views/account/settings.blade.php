@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Account'),
        'subtitle' => __('Settings')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row with-tabs justify-content-end py-2 py-md-3">
            <div class="col-6 col-lg-6 d-flex justify-content-end align-items-center">
                <ul class="nav" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link mb-sm-3 mb-md-0 text-black" id="profile-settings-subscription-tab"
                           data-toggle="tab"
                           href="#profile-settings-subscription-tab-content" role="tab"
                           aria-controls="tabs-icons-text-1"
                           aria-selected="true">
                            {{ __('Subscription') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 text-black" id="profile-settings-invoices-tab"
                           data-toggle="tab"
                           href="#profile-settings-invoices-tab-content" role="tab"
                           aria-controls="tabs-icons-text-2" aria-selected="false">
                            {{ __('Invoices') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 text-black" id="profile-settings-payment-tab"
                           data-toggle="tab"
                           href="#profile-settings-payment-tab-content" role="tab"
                           aria-controls="tabs-icons-text-2" aria-selected="false">
                            {{ __('Payment') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 pt-3">
            <div class="card">
                <div class="col-12 mt-2 alert-container"></div>
                <div class="tab-content text-black" id="myTabContent">
                    <div class="tab-pane fade show active p-3" id="profile-settings-subscription-tab-content"
                         role="tabpanel"
                         aria-labelledby="profile-user-information-tab">
                        @include('account.settings.subscription')
                    </div>
                    <div class="tab-pane fade p-3" id="profile-settings-invoices-tab-content" role="tabpanel"
                         aria-labelledby="profile-settings-invoices-tab">
                        @include('account.settings.invoices')
                    </div>
                    <div class="tab-pane fade p-3" id="profile-settings-payment-tab-content" role="tabpanel"
                         aria-labelledby="profile-settings-payment-tab">
                        @include('account.settings.payment')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new ProfileForm()
    </script>
@endpush
