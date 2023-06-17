@extends('layouts.app', ['title' => __('Warehouse Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Edit Warehouse') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nav-wrapper">
                            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                                <li class="nav-item">
                                    <div class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab"
                                       aria-controls="tabs-icons-text-1" aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>Warehouse</div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" href="{{ route('warehouses.editWarehouseLocation', [ 'warehouse' => $warehouse ]) }}" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>Locations</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <form method="post" action="{{ route('warehouses.update', [ 'warehouse' => $warehouse, 'id' =>  $warehouse->id]) }}" autocomplete="off">
                                    @csrf
                                    <h6 class="heading-small text-muted mb-4">{{ __('Warehouse information') }}</h6>
                                    <div class="pl-lg-4">
                                        {{ method_field('PUT') }}
                                        @include('shared.forms.contactInformationFields', [
                                            'name' => 'contact_information',
                                            'contactInformation' => $warehouse->contactInformation
                                        ])
                                        @include('shared.forms.ajaxSelect', [
                                         'url' => route('warehouses.filterCustomers', ['customerId' => $warehouse->customer->contactInformation->id]),
                                         'name' => 'customer_id',
                                         'className' => 'ajax-user-input',
                                         'placeholder' => __('Search for a customer'),
                                         'label' => __('Change customer'),
                                         'default' => [
                                                'id' => $warehouse->customer->id,
                                                'text' => $warehouse->customer->contactInformation->name
                                            ]
                                        ])
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

