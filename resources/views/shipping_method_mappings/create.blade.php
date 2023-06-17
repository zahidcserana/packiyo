@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Shipping Methods'),
        'subtitle' => __('Add'),
        'button'=> [
            'title' => __('Back to list'),
            'href' => route('shipping_method_mapping.index')
        ]
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('shipping_method_mapping.store') }}" autocomplete="off">
                            @csrf

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
