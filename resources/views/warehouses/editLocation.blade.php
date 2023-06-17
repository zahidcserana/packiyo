
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
                                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-1-tab"
                                         aria-controls="tabs-icons-text-1" aria-selected="true" href="{{ route('warehouses.edit', [ 'warehouse' => $warehouse ]) }}"><i class="ni ni-cloud-upload-96 mr-2"></i>Warehouse</a>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-2-tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>Locations</div>
                                </li>
                            </ul>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <h6 class="heading-small text-muted mb-4">{{ __('Edit locations') }}</h6>
                                <div class="col-12 text-right">
                                    <a href="{{ route('warehouseLocation.create',['warehouse' => $warehouse]) }}" class="btn btn-sm btn-primary">{{ __('Add location') }}</a>
                                </div>
                                <br>
                                <table class="table align-items-center table-flush datatable-basic">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Location') }}</th>
                                        <th scope="col">{{ __('Pickable') }}</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($warehouse->locations as $location)
                                        <tr>
                                            <td>{{ $location->name }}</td>
                                            <td>{{ $location->isPickable() ? __('Yes') : __('No') }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('warehouseLocation.edit', [ 'warehouse' => $warehouse, 'location' => $location ]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                                                <form action="{{ route('warehouseLocation.destroy', ['location' => $location, 'warehouse' => $warehouse, 'id' => $location->id, 'warehouse_id' => $location->warehouse_id]) }}" method="post" style="display: inline-block">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger" data-confirm-message="{{ __('Are you sure you want to delete this location?') }}">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
