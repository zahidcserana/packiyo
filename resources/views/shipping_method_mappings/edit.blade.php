@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Shipping Methods'),
        'subtitle' => __('Edit')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('shipping_method_mapping.index') }}" class="text-black font-sm font-weight-600 d-inline-flex align-items-center bg-white border-8 px-3 py-2 mt-3">
                <i class="picon-arrow-backward-filled icon-lg icon-black mr-1"></i>
                {{ __('Back') }}
            </a>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('shipping_method_mapping.update', ['shipping_method_mapping' => $shippingMethodMapping]) }}" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <h6 class="heading-small text-muted mb-4">{{ __('Method information') }}</h6>

                            <div class="pl-lg-4">
                                @include('shipping_method_mappings.shippingMethodMappingInformationFields')
                                <div class="text-center">
                                    <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">{{ __('Save') }}</button>
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
        new ShippingMethodMapping()
    </script>
@endpush
