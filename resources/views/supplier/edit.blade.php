
@extends('layouts.app', ['title' => __('Supplier Management')])

@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Edit Supplier') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card shadow">
                            <div class="card-body">
                                <form method="post" action="{{ route('supplier.update', [ 'supplier' => $supplier, 'id' => $supplier->id ]) }}" autocomplete="off">
                                    @csrf
                                    <h6 class="heading-small text-muted mb-4">{{ __('Supplier information') }}</h6>
                                    <div class="pl-lg-4">
                                        {{ method_field('PUT') }}
                                        @include('supplier.supplierInformationFields', [
                                            'supplier' => $supplier
                                        ])
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </form>
                                @include('supplier.supplierProductsInformation', [
                                    'products' => $supplier->products ?? []
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

