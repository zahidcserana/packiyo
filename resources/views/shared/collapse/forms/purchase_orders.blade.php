@include('shared.collapse.forms._customer')
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Purchase Order Status') }}</label>
    <select name="purchase_order_status" id="" class="form-control" id="orderStatuses">
        <option value="all">{{ __('All') }}</option>
        @foreach(\App\Models\PurchaseOrder::PURCHASE_ORDER_STATUSES as $id => $status)
            <option value="{{ $id }}">{{ $status }}</option>
        @endforeach
        @if(!empty($data['purchaseOrderStatuses']) && count($data['purchaseOrderStatuses']))
            @foreach ($data['purchaseOrderStatuses'] as $id => $status)
                <option value="{{ $id }}">{{ $status }}</option>
            @endforeach
        @endif
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Warehouse') }}</label>
    <select name="warehouse" class="form-control">
        <option value="0">{{ __('All') }}</option>
        @if(! empty($data['warehouses']) && count($data['warehouses']))
            @foreach($data['warehouses'] as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->contactInformation->name }}</option>
            @endforeach
        @endif
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Received') }}</label>
    <select name="received" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="0">{{ __('Yes') }}</option>
        <option value="1">{{ __('No') }}</option>
    </select>
</div>
@include('shared.forms.editSelectTag', [
    'containerClass' => 'form-group col-12 col-md-3',
    'labelClass' => '',
    'selectClass' => 'select-ajax-tags',
    'label' => __('Tags'),
    'minimumInputLength' => 3
])
