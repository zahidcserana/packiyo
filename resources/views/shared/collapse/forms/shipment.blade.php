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
    <label for="" class="font-xs">{{ __('Carrier') }}</label>
    <select name="shipping_carrier" class="form-control">
        <option value="0">{{ __('All') }}</option>
        @foreach($data['shipping_carriers'] as $shippingCarrier)
            <option>{{ $shippingCarrier }}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Shipping Method') }}</label>
    <select name="shipping_method" class="form-control">
        <option value="0">{{ __('All') }}</option>
        @foreach($data['shipping_methods'] as $shippingMethod)
            <option>{{ $shippingMethod }}</option>
        @endforeach
    </select>
</div>
