@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Picking Carts'), 'subtitle' => __('Add'), 'button'=>['title' => __('Back to list'), 'href' => route('picking_carts.index')]])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive p-4">
                        <form method="post" action="{{ route('picking_carts.store') }}" id="tote-user-form" autocomplete="off">
                            @csrf
                            <div class="pl-lg-4">
                                @include('picking_carts.pickingCartFormFields', [
                                    'createForm' => true
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

