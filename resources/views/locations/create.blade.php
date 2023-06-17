@extends('layouts.app', ['title' => __('Warehouse Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Add Location') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('warehouses.editWarehouseLocation', ['warehouse' => $warehouse]) }}" class="btn btn-sm btn-primary">{{ __('Back to location list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('warehouseLocation.store', ['warehouse' => $warehouse, 'warehouse_id' => $warehouse->id]) }}" autocomplete="off">
                            @csrf
                            <h6 class="heading-small text-muted mb-4">{{ __('Warehouse information') }}</h6>
                            <div class="pl-lg-4">
                                @include('locations.locationInformationFields')
                                <div class="pl-lg-4">
                                    <div class="table-responsive p-4">
                                        <table class="col-12 table align-items-center table-flush">
                                            <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('Product') }}</th>
                                                <th scope="col">{{ __('Quantity on hand') }}</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="item_container">
                                            <tr class="order-item-fields">
                                                <td style="white-space: unset">
                                                    @include('shared.forms.ajaxSelect', [
                                                        'url' => route('location.filterProducts', ['location' => null]),
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

