@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Settings',
        'subtitle' => 'Manage Users',
    ])

    <div class="container-fluid formsContainer userContainer">
        <div class="row px-3">
            <form
                class="col-12 border-12 py-3 px-4 m-0 mb-3 bg-white smallForm userForm editable"
                data-action="{{ route('user.store') }}"
                autocomplete="off"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="border-bottom py-2 d-flex align-items-center">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">
                        {{ __('User Information') }}
                    </h6>
                    @include('shared.buttons.sectionEditButtons', ['hideEditButton' => true])
                </div>

                <div class="d-flex text-center py-3 overflow-auto justify-content-between flex-column">
                    <div class="w-100">
                        <div class="row w-100">
                            <div class="col col-12">
                                @include('shared.forms.contactInformationFields', [
                                    'name' => 'contact_information'
                                ])
                            </div>
                        </div>
                        <div class="row w-100 pt-2">
                            <div class="col-6 pr-0">
                                @include('shared.forms.input', [
                                   'name' => 'email',
                                   'label' => __('User Email'),
                                   'type' => 'email',
                                   'value' => ''
                               ])
                            </div>
                            <div class="col-6 pl-0">
                                <div class="searchSelect">
                                    @include('shared.forms.select', [
                                       'name' => 'user_role_id',
                                       'containerClass' => 'mx-2 text-left d-flex flex-column justify-content-end mb-3',
                                       'label' => __('Role'),
                                       'placeholder' => __('User Role'),
                                       'error' => false,
                                       'value' => '',
                                       'options' => $roles->pluck('name', 'id')
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="row w-100">
                            <div class="col-6 pr-0">
                                @include('shared.forms.input', [
                                    'name' => 'password',
                                    'label' => __('Password'),
                                    'type' => 'password'
                                ])
                            </div>
                            <div class="col-6 pl-0">
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
                                @include('shared.forms.input', [
                                    'name' => 'customer_user_role_id',
                                    'value' => \App\Models\CustomerUserRole::ROLE_DEFAULT,
                                    'type' => 'hidden'
                                ])
                                @if(auth()->user()->isAdmin())
                                    <div class="searchSelect">
                                        @include('shared.forms.select', [
                                           'name' => 'user_role_id',
                                           'containerClass' => 'mx-2 text-left d-flex flex-column justify-content-end mb-3',
                                           'label' => __('Role'),
                                           'placeholder' => __('User Role'),
                                           'error' => false,
                                           'value' => \App\Models\UserRole::ROLE_DEFAULT,
                                           'options' => $roles->pluck('name', 'id')
                                        ])
                                    </div>
                                @else
                                    @include('shared.forms.input', [
                                    'name' => 'user_role_id',
                                    'value' => \App\Models\UserRole::ROLE_DEFAULT,
                                    'type' => 'hidden'
                                ])
                                @endif
                            </div>
                        </div>
                        <div class="row w-100">
                            <div class="col-6 pr-0">
                                @include('shared.forms.input', [
                                    'name' => 'password',
                                    'label' => __('Password'),
                                    'type' => 'password'
                                ])
                            </div>
                            <div class="col-6 pl-0">
                                @include('shared.forms.input', [
                                    'name' => 'password_confirmation',
                                    'label' => __('Confirm Password'),
                                    'type' => 'password'
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <button class="globalSave p-0 border-0 bg-logoOrange align-items-center" type="button">
                <i class="picon-save-light icon-white icon-xl" title="Save"></i>
            </button>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new User
    </script>
@endpush
