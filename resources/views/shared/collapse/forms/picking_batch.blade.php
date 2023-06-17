@include('shared.collapse.forms._customer')
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Start Date') }}</label>
    <input class="form-control datetimepicker" type="text" value="{{ user_date_time(now()->subWeeks(2)->startOfDay()) }}" name="start_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('End Date') }}</label>
    <input class="form-control datetimepicker" type="text" value="" name="end_date">
</div>

<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('User') }}</label>
    <select name="user_id" class="form-control">
        <option value="">{{ __('All') }}</option>
        @foreach($data['users'] as $user)
            <option value="{{$user->id}}">{{ $user->contactInformation->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Active') }}</label>
    <select name="active" class="form-control">
        <option value="all">{{ __('All') }}</option>
        <option value="yes" selected="selected">{{ __('Yes') }}</option>
        <option value="no">{{ __('No') }}</option>
    </select>
</div>

<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Show cancelled') }}</label>
    <select name="show_cancelled" class="form-control">
        <option value="2">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0" selected>{{ __('No') }}</option>
    </select>
</div>

