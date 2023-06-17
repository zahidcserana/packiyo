@php
    $selectedCustomer = app('user')->getSelectedCustomers();
@endphp
@if ($selectedCustomer->count() > 1)
    <div class="form-group col-12 col-md-3">
        <label for="" class="font-xs">{{ __('Customer') }}</label>
        <select name="customer_id" class="form-control">
            <option value="0">{{ __('All') }}</option>
            @foreach($selectedCustomer as $customer)
                <option value="{{ $customer->id }}">{{ $customer->contactInformation->name }}</option>
            @endforeach
        </select>
    </div>
@endif
