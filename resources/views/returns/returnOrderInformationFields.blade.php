<div class="col-xl-6 sizing">
    @include('shared.forms.ajaxSelect', [
       'url' => route('return.filterOrders'),
       'name' => 'order_id',
       'className' => 'ajax-user-input order-select',
       'placeholder' => __('Search'),
       'label' => __('Order'),
       'default' => [
            'id' => $return->order_id ?? old('order_id') ?? $order->id ?? '',
            'text' => $return->order->number ?? $order->number ?? ''
        ]
    ])
    @include('shared.forms.input', [
       'name' => 'number',
       'label' => __('Number'),
       'value' => $return->number ?? ''
    ])
    @include('shared.forms.input', [
   'name' => 'reason',
   'label' => __('Reason'),
   'value' => $return->reason ?? ''
])

    @include('shared.forms.input', [
       'name' => 'notes',
       'label' => __('Notes'),
       'value' => $return->notes ?? ''
    ])
</div>
<div class="col-xl-6 sizing">
    @include('shared.forms.input', [
       'name' => 'requested_at',
       'label' => __('Requested at'),
       'class' => 'datetimepicker',
       'value' => $return->requested_at ?? ''
    ])
    @include('shared.forms.input', [
       'name' => 'expected_at',
       'label' => __('Expected at'),
       'class' => 'datetimepicker',
       'value' => $return->expected_at ?? ''
    ])
    @include('shared.forms.input', [
       'name' => 'received_at',
       'label' => __('Received at'),
       'class' => 'datetimepicker',
       'value' => $return->received_at ?? ''
    ])
    <div class="form-group dropdown">
        <label class="form-control-label">{{__('Approved')}}</label>
        <select name="approved" class="form-control" data-toggle="select">
            @if($return ?? false)
                <option value="1" {{$return->approved === 1 ? 'selected' : ''}}>Yes</option>
                <option value="0" {{$return->approved === 0 ? 'selected' : ''}}>No</option>
            @else
                <option value="1">Yes</option>
                <option value="0">No</option>
            @endif
        </select>
    </div>
</div>
