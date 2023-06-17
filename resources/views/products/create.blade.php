@extends('layouts.app', ['title' => __('Product Management')])

@section('content')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Create Product') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('product.store') }}" autocomplete="off" id="product-form">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Product information') }}</h6>
                            <div class="pl-lg-4">
                                @include('shared.forms.dropzone', [
                                       'url' => route('product.store'),
                                       'isMultiple' => true,
                                       'redirect' => route('product.index'),
                                       'formId' => 'product-form',
                                       'buttonId' => 'submit-button',
                                       'images' => ''
                                   ])
                                <hr>
                                @include('products.productInformationFields')
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4" id="submit-button">{{ __('Save') }}</button>
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
        new Product('', false)
    </script>
@endpush
