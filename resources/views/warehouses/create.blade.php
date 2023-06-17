@extends('layouts.app', ['title' => __('Warehouse Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Add Warehouse') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('warehouses.store') }}" autocomplete="off">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Warehouse information') }}</h6>
                            <div class="pl-lg-4">
                                @include('shared.forms.contactInformationFields', [
                                    'name' => 'contact_information'
                                ])
                                @include('shared.forms.dropdowns.customer_selection', [
                                    'route' => route('warehouses.filterCustomers'),
                                    'readonly' => isset($taskType->customer->id) ? 'true' : null,
                                    'id' => $warehouse->customer->id ?? old('customer_id'),
                                    'text' => $warehouse->customer->contactInformation->name ?? ''
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
@endsection
