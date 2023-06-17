@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Customers',
        'subtitle' =>  __('Create Customer')
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
                        <form method="post" id="create-customer-form" action="{{ route('customer.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Customer information') }}</h6>
                            <div class="pl-lg-4">
                                @include('shared.forms.contactInformationFields', [
                                    'name' => 'contact_information',
                                ])
                                <hr>

                                <div class="d-flex orderContactInfo flex-column">
                                    <div class="d-lg-flex">
                                        @include('shared.forms.select', [
                                                'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_LOCALE,
                                                'label' => __('Language'),
                                               'containerClass' => 'w-50 mx-2',
                                                'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_LOCALE)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_LOCALE) : false,
                                                'value' => '',
                                                'options' => [
                                                    'en' => __('English'),
                                                    'no' => __('Norwegian'),
                                                    'da' => __('Danish')
                                                ]
                                            ])

                                        @include('shared.forms.select', [
                                               'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_CURRENCY,
                                               'label' => __('Currency'),
                                               'containerClass' => 'w-50 mx-2',
                                               'value' => '',
                                               'options' => \App\Models\Currency::all()->pluck('code', 'id'),
                                               'attributes' => [
                                                    'data-no-select2' => true,
                                                    'data-placeholder' => 'Currency'
                                               ]
                                            ])
                                    </div>
                                </div>

                                @include('shared.forms.productDimensionsFields')

                                <hr>
                                <div class="flex-container" style="display: flex">
                                    <div class="flex-column col-6">
                                        @include('shared.forms.input', [
                                            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_HEADING,
                                            'label' => __('Packing Slip Heading'),
                                            'containerClass' => 'w-100 ml--2',
                                            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_HEADING)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_HEADING) : false,
                                            'value' => ''
                                        ])

                                        @include('shared.forms.textarea', [
                                            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_TEXT,
                                    'label' => __('Packing / Order Slip Text'),
                                            'containerClass' => 'w-100 ml--2',
                                            'class' => 'editor',
                                            'rows' => 2,
                                            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_TEXT)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_TEXT) : false,
                                            'value' => ''
                                        ])
                                    </div>

                                    <div class="form-group mx-1 mb-0 {{ $errors->has('packing_slip_logo') ? 'has-danger' : '' }} flex-column col-6">
                                        <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs" for="packing_slip_logo">
                                            {{ __('Packing / Order Slip Logo') }}
                                        </label>
                                        @include('shared.forms.dropzoneBasic', [
                                           'url' => route('customer.store'),
                                           'isMultiple' => false,
                                           'formId' => 'create-customer-form',
                                           'buttonId' => 'submit-button',
                                           'images' => '',
                                           'name' => 'order_slip_logo'
                                       ])
                                    </div>
                                </div>
                                <div class="form-group">
                                    @include('shared.forms.checkbox', [
                                        'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_AUTO_PRINT,
                                        'label' => ('Auto Print Order Slip'),
                                        'checked' => ''
                                    ])
                                </div>
                                <div class="form-group">
                                    @include('shared.forms.checkbox', [
                                        'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_AUTO_RETURN_LABEL,
                                        'label' => ('Create return labels while shipping'),
                                        'checked' => ''
                                        ])
                                </div>
                                <div class="form-group">
                                @include('shared.forms.checkbox', [
                                        'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_SHOW_PRICES_ON_SLIPS,
                                        'label' => ('Show prices on packing slip and order slip'),
                                        'checked' => ''
                                    ])
                                </div>
                                @if($customers->count() > 1 && auth()->user()->isAdmin())
                                    <div class="form-group">
                                        @include('shared.forms.checkbox', [
                                            'name' => 'allow_child_customers',
                                            'label' => ('Allow child customers'),
                                            'className' => 'ajax-user-input parent_customer_id',
                                            'containerClass' => 'mx-2',
                                            'checked' => ''
                                        ])
                                    </div>
                                    <div class="searchSelect">
                                        @include('shared.forms.new.ajaxSelect', [
                                        'url' => route('user.getCustomers'),
                                        'name' => 'parent_customer_id',
                                        'className' => 'ajax-user-input parent_customer_id',
                                        'placeholder' => __('Select parent customer'),
                                        'label' => __('Parent customer'),
                                        'default' => [
                                            'id' => old('parent_customer_id'),
                                            'text' => ''
                                        ]
                                    ])
                                    </div>
                                @elseif($customers->count() > 1 && !auth()->user()->isAdmin())
                                    <input type="hidden" name="allow_child_customers" value="0" />
                                    <input type="hidden" name="parent_customer_id" value="{{ $parentCustomer->id }}" />
                                @else
                                    <input type="hidden" name="allow_child_customers" value="0" />
                                    <input type="hidden" name="parent_customer_id" value="{{ $customers->first()->id }}" />
                                @endif

                                <div class="form-group">
                                    @include('shared.forms.select', [
                                       'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_PICKING_ROUTE_STRATEGY,
                                       'label' => __('Picking route strategy'),
                                       'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_PICKING_ROUTE_STRATEGY] ?? '',
                                       'options' => [
                                           \App\Components\RouteOptimizationComponent::PICKING_STRATEGY_ALPHANUMERICALLY => __('Alphanumerically'),
                                           \App\Components\RouteOptimizationComponent::PICKING_STRATEGY_MOST_INVENTORY => __('Most inventory'),
                                           \App\Components\RouteOptimizationComponent::PICKING_STRATEGY_LEAST_INVENTORY => __('Least inventory'),
                                       ]
                                    ])
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button
                                        id="submit-button"
                                        class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                                        {{ __('Create') }}
                                    </button>
                                </div>
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
        $(document).on('change', '#chk-allow_child_customers', function(){
            if($(this).prop('checked')){
                $('.parent_customer_id').attr('disabled', 'disabled');
            } else {
                $('.parent_customer_id').removeAttr('disabled');
            }
        })
    </script>
@endpush
