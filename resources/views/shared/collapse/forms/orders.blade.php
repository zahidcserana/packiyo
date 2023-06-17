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
    <label for="" class="font-xs">{{ __('Required ship date') }}</label>
    <input class="form-control datetimepicker" type="text" value="" name="shipping_date_before_at">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Order Status') }}</label>
    <select name="order_status" id="" class="form-control" id="orderStatuses">
        <option value="0">{{ __('All') }}</option>
        @foreach(\App\Models\Order::ORDER_STATUSES as $id => $status)
            <option value="{{ $id }}">{{ $status }}</option>
        @endforeach
        @foreach ($data['order_statuses'] as $status)
            <option value="{{ $status->id }}">{{ $status->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Ready To Ship') }}</label>
    <select name="ready_to_ship" class="form-control">
        <option value="all">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('In Tote') }}</label>
    <select name="in_tote" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3 d-flex">
    <div class="w-50 mr-1">
        <label for="" class="font-xs">{{ __('Priority From') }}</label>
        <input type="number" class="form-control" name="priority_from">
    </div>
    <div class="w-50 ">
        <label for="" class="font-xs">{{ __('Priority To') }}</label>
        <input type="number" class="form-control" name="priority_to">
    </div>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Backordered') }}</label>
    <select name="backordered" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="0">{{ __('Yes') }}</option>
        <option value="1">{{ __('No') }}</option>
    </select>
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
@include('shared.forms.countrySelect', [
    'containerClass' => 'col-12 col-md-3',
    'name' => 'country'
])
<div class="form-group col-12 col-md-3 d-flex">
    <div class="w-50 mr-1">
        <label for="" class="font-xs">{{ __('Weight From') }}</label>
        <input type="number" class="form-control" name="weight_from">
    </div>
    <div class="w-50 ">
        <label for="" class="font-xs">{{ __('Weight To') }}</label>
        <input type="number" class="form-control" name="weight_to">
    </div>
</div>
<div class="form-group col-12 col-md-3">
    <label for="any_hold" class="font-xs">{{ __('Order Holds') }}</label>
    <select name="any_hold" class="form-control" id="any_hold">
        <option value="all">{{ __('All') }}</option>
        <option value="any_hold">{{ __('Any') }}</option>
        <option value="operator_hold">{{ __('Operator hold') }}</option>
        <option value="payment_hold">{{ __('Payment hold') }}</option>
        <option value="address_hold">{{ __('Address hold') }}</option>
        <option value="fraud_hold">{{ __('Fraud hold') }}</option>
        <option value="allocation_hold">{{ __('Allocation hold') }}</option>
        <option value="hold_until">{{ __('Hold until') }}</option>
        <option value="none">{{ __('None') }}</option>
    </select>
</div>
@include('shared.forms.editSelectTag', [
    'containerClass' => 'form-group col-12 col-md-3',
    'labelClass' => '',
    'selectClass' => 'select-ajax-tags',
    'label' => __('Tags'),
    'minimumInputLength' => 3
])
