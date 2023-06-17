<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Start Date') }}</label>
    <input class="form-control datetimepicker" type="text" value="{{ user_date_time(now()->subWeeks(2)->startOfDay()) }}" name="start_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('End Date') }}</label>
    <input class="form-control datetimepicker" type="text" value="" name="end_date">
</div>
<div class="form-group col-12 col-md-6 col-lg-3">
    <label for="" class="font-xs">{{ __('Printed') }}</label>
    <select name="printed" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="yes">{{ __('Yes') }}</option>
        <option value="no">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-6 col-lg-3">
    <label for="" class="font-xs">{{ __('Packed') }}</label>
    <select name="packed" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="yes">{{ __('Yes') }}</option>
        <option value="no">{{ __('No') }}</option>
    </select>
</div>
