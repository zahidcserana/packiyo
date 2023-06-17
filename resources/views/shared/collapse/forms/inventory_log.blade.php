<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{__('Start Date')}}</label>
    <input class="form-control datetimepicker" type="text" value="" name="start_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{__('End Date')}}</label>
    <input class="form-control datetimepicker" type="text" value="" name="end_date">
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{__('Reason')}}</label>
    <select name="reason" id="" class="form-control" id="searchReasons">
        <option value="0">{{__('All')}}</option>
        @foreach ($data['reasons'] as $reason)
            <option value="{{ $reason }}">{{ $reason }}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{__('Warehouse')}}</label>
    <select name="warehouse" id="" class="form-control" id="searchWarehouse">
        <option value="0">{{__('All')}}</option>
        @foreach ($data['warehouses'] as $warehouse)
            <option value="{{ $warehouse->id }}">{{ $warehouse->contactInformation->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-12 col-md-3">
    <label for="name" class="font-xs">{{__('Location')}}</label>
    <input type="text" name="location" class="form-control" id="searchLocation"/>
</div>
<div class="form-group col-12 col-md-3">
    <label for="name" class="font-xs">{{__('Product')}}</label>
    <input type="text" name="product" class="form-control" id="searchProduct"/>
</div>
<div class="form-group col-12 col-md-3">
    <label for="" class="font-xs">{{__('Change by')}}</label>
    <select name="change_by" id="" class="form-control" id="searchChangeBy">
        <option value="0">{{__('All')}}</option>
        @foreach ($data['users'] as $user)
            <option value="{{ $user->id }}">{{ $user->contactInformation ? $user->contactInformation->name : $user->email }}</option>
        @endforeach
    </select>
</div>
