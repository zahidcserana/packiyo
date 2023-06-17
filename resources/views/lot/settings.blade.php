@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Lot Tracking'), 'subtitle' => __('Settings')])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row border-12 p-0 m-2 mb-5 bg-white ">
                    <div class="col col-12 p-0">
                        <div class="card-calendar">
                            <div class="card-header pb-0">
                                <p>&nbsp;</p>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <form method="post" action="{{ route('lot.settings') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group calendar-input">
                                                <label>Priority</label>
                                                <select name="priority" class="form-control">
                                                    <option @if(!is_null($settings) && $settings->priority == \App\Models\Lot::FEFO_ID) selected @endif value="{{\App\Models\Lot::FEFO_ID}}">{{__('FEFO (First Expired First Out')}}</option>
                                                    <option @if(!is_null($settings) && $settings->priority == \App\Models\Lot::FIFO_ID) selected @endif value="{{\App\Models\Lot::FIFO_ID}}">{{__('FIFO (First In First Out)')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group calendar-input">
                                                <label>Days to disable picking before product expiration</label>
                                                <input class="form-control" type="number" min="1" name="disable_picking_days" value="{{$settings->disable_picking_days ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group calendar-input">
                                                <label>Notifications email</label>
                                                <input class="form-control" type="email" name="notification_email" value="{{$settings->notification_email ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <button type="button" id="filter-widgets" class="col-12 btn bg-logoOrange text-white borderOrange">{{ __('Save') }}</button>
                                        </div>
                                        <div class="form-group col-6">
                                            <button type="reset" class="col-12 btn borderOrange resetButton">{{ __('Reset') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--             @include('shared.draggable.dashboard_container')--}}
            <div class="col-12">
                @include('shared.draggable.dashboard_widgets_container')
            </div>
        </div>
        @include('layouts.footers.auth')
    </div>
@endsection
