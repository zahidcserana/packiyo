@include('shared.collapse.forms._customer')
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Start Date') }}</label>
    <input class="form-control dt-daterangepicker" type="text" value="" name="start_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('End Date') }}</label>
    <input class="form-control datetimepicker" type="text" value="" name="end_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Has not sold in') }}</label>
    <select name="has_not_sold_in" class="form-control">
        <option value="0">{{ __('Show all') }}</option>
        <option value="30">{{ __('30 days') }}</option>
        <option value="60">{{ __('60 days') }}</option>
        <option value="90">{{ __('90 days') }}</option>
        <option value="180">{{ __('180 days') }}</option>
        <option value="365">{{ __('365 days') }}</option>
    </select>
</div>
