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
                                         aria-controls="tabs-icons-text-1" aria-selected="false" href="{{ route('customer.edit', [ 'customer' => $customer ]) }}"><i class="ni ni-cloud-upload-96 mr-2"></i>{{ __('Customer') }}</a>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-2-tab" aria-controls="tabs-icons-text-2" aria-selected="true"><i class="ni ni-bell-55 mr-2"></i>{{ __('Users') }}</div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" href="{{ route('customers.easypost_credentials.index', [ 'customer' => $customer ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>{{__('Easypost Credentials')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" href="{{ route('customers.webshipper_credentials.index', [ 'customer' => $customer ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>{{__('Webshipper Credentials')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="main-component">
                                    <div class="table-responsive py-4">
                                        <form id="customer-user-form" action="{{route('customer.updateUsers', [ 'customer' => $customer])}}" method="post" >@csrf</form>
                                        <table class="table align-items-center table-flush items-table">
                                            <thead>
                                                <tr class="text-black">
                                                    <th scope="col">{{ __('Name') }}</th>
                                                    <th scope="col">{{ __('Email') }}</th>
                                                    <th scope="col">{{ __('Role') }}</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($customer->users as $key => $user)
                                                <tr>
                                                    <td>{{ $user->contactInformation->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <input form="customer-user-form" type="hidden" name="customer_user[{{ $key }}][user_id]" value="{{ $user->id }}" />
                                                        <select form="customer-user-form" name="customer_user[{{ $key }}][role_id]" class="form-control" data-toggle="select" data-placeholder="{{$user->pivot->role_id}}">
                                                            @foreach($roles as $role)
                                                                <option value="{{$role->id}}" {{$user->pivot->role_id === $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-right">
                                                        <form action="{{ route('customer.detachUser', ['customer' => $customer, 'user' => $user]) }}" method="post" style="display: inline-block">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="table-icon-button" data-confirm-message="{{ __('Are you sure you want to delete this user?') }}" data-confirm-button-text="Delete">
                                                                <i class="picon-trash-filled del_icon icon-lg" title="Delete"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                        @csrf
                                        @include('shared.forms.ajaxSelect', [
                                            'url' => route('customer.filterUsers', [ 'customer' => $customer]),
                                            'name' => 'new_user_id',
                                            'className' => 'ajax-user-input',
                                            'placeholder' => 'Search for a user to add',
                                            'label' => 'User search',
                                            'form' => "customer-user-form"
                                        ])
                                    @if(auth()->user()->isAdmin())
                                    <select form="customer-user-form mx-2" name="new_user_role_id" class="form-control" data-toggle="select" data-placeholder="">
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    <br>
                                    @endif
                                    <br>
                                    <div class="d-flex justify-content-center">
                                        <button
                                            type="submit"
                                            name="main-save"
                                            value="Save"
                                            form="customer-user-form"
                                            class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

