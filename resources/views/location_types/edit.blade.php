@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Location Types'), 'subtitle' => __('Edit'), 'button'=> ['title' => __('Back to list'), 'href' => route('location_type.index')]])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('location_type.update', ['location_type' => $locationType, 'id' => $locationType->id]) }}" autocomplete="off">
                            @csrf
                            {{ method_field('PUT') }}
                            <div class="d-flex orderContactInfo flex-column">
                                @if(!isset($sessionCustomer))
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
                                    <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
                                @endif
                                <div>
                                    @include('shared.forms.input', [
                                        'name' => 'name',
                                        'containerClass' => 'w-50',
                                        'label' => __('Name'),
                                        'value' => $locationType->name ?? ''
                                    ])
                                </div>
                                <div class="mb-4">

                                </div>
                                <div class="ml-2 d-flex">
                                    @include('shared.forms.select', [
                                        'name' => 'pickable',
                                        'label' => __('Pickable'),
                                        'containerClass' => 'w-50',
                                        'value' => $locationType->pickable ?? \App\Models\LocationType::PICKABLE_NOT_SET,
                                        'options' => [
                                            \App\Models\LocationType::PICKABLE_NO => __('No'),
                                            \App\Models\LocationType::PICKABLE_YES => __('Yes'),
                                            \App\Models\LocationType::PICKABLE_NOT_SET => __('Not Set')
                                        ]
                                    ])
                                    @include('shared.forms.select', [
                                        'name' => 'sellable',
                                        'label' => __('Sellable'),
                                        'containerClass' => 'w-50 ml-2',
                                        'value' => $locationType->sellable ?? \App\Models\LocationType::SELLABLE_NOT_SET,
                                        'options' => [
                                            \App\Models\LocationType::SELLABLE_NO => __('No'),
                                            \App\Models\LocationType::SELLABLE_YES => __('Yes'),
                                            \App\Models\LocationType::SELLABLE_NOT_SET => __('Not Set')
                                        ]
                                    ])
                                </div>
                                <div class="mb-4">

                                </div>
                                <div class="ml-2 d-flex">
                                    @include('shared.forms.select', [
                                        'name' => 'disabled_on_picking_app',
                                        'label' => __('Disabled on picking app'),
                                        'containerClass' => 'w-50',
                                        'value' => $locationType->disabled_on_picking_app ?? \App\Models\LocationType::DISABLED_ON_PICKING_APP_NOT_SET,
                                        'options' => [
                                            \App\Models\LocationType::DISABLED_ON_PICKING_APP_NO => __('No'),
                                            \App\Models\LocationType::DISABLED_ON_PICKING_APP_YES => __('Yes'),
                                            \App\Models\LocationType::DISABLED_ON_PICKING_APP_NOT_SET => __('Not Set')
                                        ]
                                    ])
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">{{ __('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
