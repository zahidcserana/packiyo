@include('shared.collapse.forms._customer')
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
    <label for="" class="font-xs">{{ __('Pickable') }}</label>
    <select name="pickable" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Sellable') }}</label>
    <select name="sellable" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
