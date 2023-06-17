@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => __('Shipping Method'),
        'subtitle' => __('Edit'),
        'button'=> [
            'title' => __('Back to list'),
            'href' => route('shipping_method.index')
        ]
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('shipping_method.update', ['shipping_method' => $shippingMethod]) }}" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <div>
                                @include('shared.forms.editSelectTag', [
                                    'containerClass' => 'form-group mb-0 mx-2 text-left mb-3',
                                    'labelClass' => 'd-flex',
                                    'selectClass' => 'select-ajax-tags',
                                    'label' => __('Tags'),
                                    'minimumInputLength' => 3,
                                    'default' => $shippingMethod->tags,
                                ])

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
        new ShippingMethod()
    </script>
@endpush
