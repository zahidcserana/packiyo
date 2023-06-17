@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Order Status'), 'subtitle' => __('Add'), 'button'=>['title' => __('Back to list'), 'href' => route('order_status.index')]])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('order_status.store') }}" autocomplete="off">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Order status information') }}</h6>

                            <div class="d-flex orderContactInfo flex-column">
                                @include('order_status.orderStatusInformationFields')
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">{{ __('Save') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
