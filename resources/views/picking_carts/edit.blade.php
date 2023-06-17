@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Picking Carts'), 'subtitle' => __('Edit'), 'button'=>['title' => __('Back to list'), 'href' => route('picking_carts.index')]])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <div class="row w-100">
                            <div class="col-12">
                                <form method="post" action="{{ route('picking_carts.update', [ 'picking_cart' => $cart ]) }}" autocomplete="off">
                                    @csrf
                                    <div class="pl-lg-4">
                                        {{ method_field('PUT') }}
                                        @include('picking_carts.pickingCartFormFields', [
                                            'cart' => $cart,
                                            'createForm' => false
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
        </div>
    </div>
@endsection

