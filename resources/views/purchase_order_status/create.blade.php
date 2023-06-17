@extends('layouts.app', ['title' => __('Order Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Add Purchase Order status') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('purchase_order_status.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('purchase_order_status.store') }}" autocomplete="off">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Purchase Order status information') }}</h6>
                            <div class="pl-lg-4">
                               @include('purchase_order_status.purchaseOrderStatusInformationFields')
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
