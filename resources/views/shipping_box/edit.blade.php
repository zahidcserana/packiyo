@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Packaging'), 'subtitle' => __('Edit'), 'button'=>['title' => __('Back to list'), 'href' => route('shipping_box.index')]])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('shipping_box.update', ['shipping_box' => $shippingBox, 'id' => $shippingBox->id]) }}" autocomplete="off">
                            @csrf
                            {{ method_field('PUT') }}
                            <div class="pl-lg-4">
                                @include('shipping_box.shippingBoxInformationFields', [
                                    '$shippingBox' => $shippingBox
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
