@include('shared.collapse.forms._customer')
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Allocated') }}</label>
    <select name="allocated" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Backordered') }}</label>
    <select name="backordered" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('In Stock') }}</label>
    <select name="in_stock" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Vendor') }}</label>
    <select name="supplier" class="form-control">
        <option value="0">{{ __('All') }}</option>

        @foreach($data['suppliers'] ?? [] as $supplier)
            <option value="{{ $supplier->id }}">
                {{ $supplier->contactInformation->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Warehouse') }}</label>
    <select name="warehouse" class="form-control">
        <option value="0">{{ __('All') }}</option>

        @foreach($data['warehouses'] ?? [] as $warehouse)
            <option value="{{ $warehouse->id }}">
                {{ $warehouse->contactInformation->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Kit') }}</label>
    <select name="is_kit" class="form-control">
        <option value="">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0">{{ __('No') }}</option>
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{ __('Show deleted') }}</label>
    <select name="show_deleted" class="form-control">
        <option value="2">{{ __('All') }}</option>
        <option value="1">{{ __('Yes') }}</option>
        <option value="0" selected>{{ __('No') }}</option>
    </select>
</div>
@include('shared.forms.editSelectTag', [
    'containerClass' => 'form-group col-12 col-md-3',
    'labelClass' => '',
    'selectClass' => 'select-ajax-tags',
    'label' => __('Tags'),
    'minimumInputLength' => 3
])
