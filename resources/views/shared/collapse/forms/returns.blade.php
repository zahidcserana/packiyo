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
    <label for="" class="font-xs">SKU</label>
    <select name="sku" id="" class="form-control" id="searchSku">
        <option value="0">{{ __('All') }}</option>
        @if(! empty($data['skus']) && count($data['skus']))
            @foreach ($data['skus'] as $sku)
                <option value="{{ $sku->sku }}">{{ $sku->sku }}</option>
            @endforeach
        @endif
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Return Status') }}</label>
    <select name="return_status" id="" class="form-control" id="orderStatuses">
        <option value="0">{{ __('All') }}</option>
        <option value="pending">{{ __('Pending') }}</option>
        @if(! empty($data['returnStatuses']) && count($data['returnStatuses']))
            @foreach ($data['returnStatuses'] as $id => $status)
                <option value="{{ $id }}">{{ $status }}</option>
            @endforeach
        @endif
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Warehouse') }}</label>
    <select name="warehouse" id="" class="form-control" id="searchWarehouse">
        <option value="0">All</option>
        @if(! empty($data['warehouses']) && count($data['warehouses']))
            @foreach ($data['warehouses'] as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->contactInformation->name }}</option>
            @endforeach
        @endif
    </select>
</div>
@include('shared.forms.editSelectTag', [
    'containerClass' => 'form-group col-12 col-md-3',
    'labelClass' => '',
    'selectClass' => 'select-ajax-tags',
    'label' => __('Tags'),
    'minimumInputLength' => 3
])
