@foreach($orderItems as $key => $item)
    <tr id="item[{{$key}}]" class="order-item-fields">
        <td style="white-space: unset">
            @include('shared.forms.ajaxSelect', [
                'url' => route('return.filterOrderProducts', [ 'orderId' => $item->order_id ?? old('order_id') ]),
                'name' => 'return_items[' . $key . '][product_id]',
                'className' => 'ajax-user-input orderProduct reset-value',
                'placeholder' => __('Search'),
                'label' => '',
                'default' => [
                    'id' => isset($item->product) ? $item->product->id : $item['product_id'],
                    'text' => isset($item->product) ? 'SKU: ' . $item->product->sku . ', NAME: ' . $item->product->name : ''
                ]
            ])
        </td>
        <td>
            @include('shared.forms.input', [
              'name' => 'return_items[' . $key . '][quantity]',
              'label' => '',
              'type' => 'number',
              'class' => 'reset-value reset_on_delete',
              'value' => isset($item->quantity) ? $item->quantity_shipped - $item['quantity_returned'] : 0
           ])
        </td>
        <td>
            @include('shared.forms.input', [
                 'name' => 'return_items[' . $key . '][quantity_received]',
                 'label' => '',
                 'type' => 'number',
                 'class' => 'reset-value received-quantity reset_on_delete',
                 'value' => 0,
                 'readOnly' => 'readonly'
             ])
        </td>
        <td>
            <button type="button" class="btn btn-danger delete-item">
                {{ __('Delete') }}
            </button>
        </td>
    </tr>
@endforeach
