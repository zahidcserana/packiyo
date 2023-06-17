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
    <label for="" class="font-xs">{{ __('Bulk Shipped') }}</label>
    <select name="bulk_shipped" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
