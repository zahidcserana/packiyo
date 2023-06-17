@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth', [ 'title' => __('Sales Activity'), 'subtitle' => __('')])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row border-12 p-0 m-2 mb-5 bg-white ">
                    <div class="col col-12 p-0">
                        <div class="card-calendar">
                            <div class="card-header pb-0">
                                <p>{{ __('Filter by date to get updated results') }}</p>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <form method="post" action="{{ route('user_settings.dashboard_settings') }}"
                                      autocomplete="off"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3 col-12">
                                            <div class="form-group calendar-input">
                                                <input class="form-control" type="text" name="{{ \App\Models\UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_START }}" value="{{$dashboardFilterDateStart ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group calendar-input">
                                                <input class="form-control" type="text" name="{{ \App\Models\UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_END }}" value="{{$dashboardFilterDateEnd ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-group">
                                                <button type="button" id="filter-widgets" class="col-12 btn bg-logoOrange text-white borderOrange">{{ __('Filter Now') }}</button>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-group">
                                                <button type="reset" class="col-12 btn borderOrange resetButton">{{ __('Reset') }}</button>
                                            </div>
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
@push('js')
    @if($showGeoWidget)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=&v=weekly"></script>
    @endif
    <script>
        $(function() {
            let DEFAULT_DATE_RANGE = '{{($defDayDif*2)}}';
            let DEFAULT_DASHBOARD_DATE_RANGE = '{{$defDayDif}}';

            let startDate = $('input[name="dashboard_filter_date_start"]');
            let startDateValue = startDate.val() ? new Date(startDate.val()) : moment().subtract(DEFAULT_DASHBOARD_DATE_RANGE, 'd').format('Y-MM-DD');

            startDate.daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                },
                singleDatePicker: true,
                showDropdowns: true,
                startDate: startDateValue
            });

            let endDate = $('input[name="dashboard_filter_date_end"]');
            let endDateValue = endDate.val() ? new Date(endDate.val()) : new Date();

            endDate.daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                },
                singleDatePicker: true,
                showDropdowns: true,
                startDate: endDateValue
            });
        })
    </script>
@endpush
