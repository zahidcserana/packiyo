@extends('layouts.app')

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
                                       aria-controls="tabs-icons-text-1" aria-selected="false" href="{{ route('customer.edit', [ 'customer' => $customer ]) }}"><i class="ni ni-cloud-upload-96 mr-2"></i>Customer</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab"
                                       aria-controls="tabs-icons-text-2" aria-selected="false" href="{{ route('customer.editUsers', [ 'customer' => $customer ]) }}"><i class="ni ni-bell-55 mr-2"></i>Users</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" href="{{ route('customers.easypost_credentials.index', [ 'customer' => $customer ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>{{__('Easypost Credentials')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" href="{{ route('customers.webshipper_credentials.index', [ 'customer' => $customer ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>{{__('Webshipper Credentials')}}</a>
                                </li>
                            </ul>
                        </div>
                        <form method="post" action="{{ route('customer.update', [ 'customer' => $customer ]) }}" autocomplete="off">
                            @csrf
                            {{ method_field('PUT') }}
                            <div class="card shadow">
                                <div class="card-body">
                                    <h6 class="heading-small text-muted mb-4">{{ __('Custom CSS Rules') }}</h6>
                                    <div class="pl-lg-4 css-overrides-container">
                                        <textarea name="customer_css" hidden></textarea>
                                        <div id="css-overrides">{{ $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_CUSTOMER_CSS] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button
                                    class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let textarea = $('textarea[name="customer_css"]')

        let editor = ace.edit('css-overrides')

        textarea.val(editor.getSession().getValue())

        editor.setTheme('ace/theme/dracula')
        editor.session.setMode('ace/mode/css')

        editor.setFontSize('18px')

        editor.setOptions({
            showPrintMargin: false
        })

        editor.getSession().on('change', function(){
            textarea.val(editor.getSession().getValue())
        })
    </script>
@endpush
