@extends('layouts.app', ['title' => __('Warehouse Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Edit Location') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('location.index') }}" class="btn btn-sm btn-primary">{{ __('Back to location list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('location.update', ['location' => $location, 'id' => $location->id, 'warehouse_id' => $location->warehouse->id]) }}" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <h6 class="heading-small text-muted mb-4">{{ __('Warehouse information') }}</h6>
                            <div class="pl-lg-4">
                                @include('locations.locationInformationFields' , [
                                    'location' => $location
                                ])
                                <br/>
                                <div class="pl-lg-4">
                                    <div class="table-responsive">
                                        <input type="hidden" id="location" value="{{$location->id}}">
                                        <table class="col-12 table align-items-center table-flush">
                                            <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('Product') }}</th>
                                                <th scope="col">{{ __('Quantity on hand') }}</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="item_container">
                                            @if(count($location->products) == 0 )
                                                <tr class="order-item-fields">
                                                    <td style="white-space: unset">
                                                        @include('shared.forms.ajaxSelect', [
                                                            'url' => route('location.filterProducts', ['location' => $location]),
                                                            'name' => 'location_product[0][product_id]',
                                                            'className' => 'ajax-user-input',
                                                            'placeholder' => __('Search for a product to add'),
                                                            'labelClass' => 'd-block',
                                                            'label' => ''
                                                        ])
                                                    </td>
                                                    <td>
                                                        @include('shared.forms.input', [
                                                            'name' => 'location_product[0][quantity_on_hand]',
                                                            'type' => 'number',
                                                            'label' => '',
                                                            'value' => 0,
                                                            'class' => 'reset_on_delete'
                                                        ])
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger delete-item">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endif
                                            @foreach($location->products as $product)
                                                <input type="hidden" name="location_product[{{$product->id}}][location_product_id]" value="{{$product->id}}">
                                                <tr class="order-item-fields">
                                                    <td style="white-space: unset">
                                                        <input id="selected_product" value="{{$product->product_id}}" type="hidden">
                                                        @include('shared.forms.ajaxSelect', [
                                                            'url' => route('location.filterProducts', ['location' => $location]),
                                                            'name' => 'location_product[' . $product->id . '][product_id]',
                                                            'className' => 'ajax-user-input',
                                                            'placeholder' => __('Search for a product to add'),
                                                            'labelClass' => 'd-block',
                                                            'label' => '',
                                                            'default' => [
                                                                'id' => $product->product_id,
                                                                'text' => 'SKU: ' . $product->product['sku'] . ', NAME: ' . $product->product['name']
                                                            ],
                                                        ])
                                                    </td>
                                                    <td>
                                                        @include('shared.forms.input', [
                                                            'name' => 'location_product[' . $product->id . '][quantity_on_hand]',
                                                            'type' => 'number',
                                                            'label' => '',
                                                            'class' => 'quantity_on_hand',
                                                            'value' => $product->quantity_on_hand,
                                                            'class' => 'reset_on_delete'
                                                        ])
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary transfer" data-toggle="modal" data-target="#modal-form">
                                                            {{ __('Transfer') }}
                                                        </button>
                                                        <button type="button" class="btn btn-danger delete-item">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <button id="add_item" class="btn btn-success mt-4">{{ __('Add more items') }}</button>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>


                        <div class="col-md-4">
                            <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="card-header">
                                            <label>Transfer product</label>
                                        </div>
                                        <div class="card-body px-lg-5 py-lg-5">
                                            <form method="post" action="{{ route('location.transfer', ['location' => $location]) }}">
                                                @csrf
                                                <input name="product_id" id="productId" type="hidden">
                                                @include('shared.forms.input', [
                                                    'label' => __('From:'),
                                                    'name' => '',
                                                    'readOnly' => 'readonly',
                                                    'value' => $location->name
                                                ])
                                                @include('shared.forms.ajaxSelect', [
                                                    'url' => route('location.filterLocations'),
                                                    'name' => 'destination_id',
                                                    'className' => 'ajax-user-input',
                                                    'placeholder' => __('Search for a location'),
                                                    'label' => 'To:'
                                                ])
                                                @include('shared.forms.input', [
                                                    'name' => 'quantity',
                                                    'label' => __('Quantity:')
                                                ])
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-primary my-4">Transfer</button>
                                                </div>
                                            </form>
                                        </div>
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

@push('js')
    <script>
        new Location
    </script>
@endpush
