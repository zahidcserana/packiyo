<div class="table-responsive pb-2 padding-sm items-table has-scrollbar">
    <table class="col-12 table align-items-center table-flush">
        <thead>
        <tr>
            <th scope="col">{{ __('Tote name') }}</th>
            <th scope="col">{{ __('Product') }}</th>
            <th scope="col">{{ __('Quantity picked') }}</th>
            <th scope="col">{{ __('Date') }}</th>
            <th scope="col">{{ __('Picked by') }}</th>
            <th scope="col">{{ __('Batch') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $order->orderItems as $orderItem )
            @foreach ($orderItem->toteOrderItems as $key => $toteOrderItem)
                <tr>
                    <td>
                        <a href="{{ route('tote.edit', $toteOrderItem->tote) }}" target="_blank">{{ $toteOrderItem->tote->name }}</a>
                    </td>
                    <td>
                        {{ __('SKU') }}: {{ $toteOrderItem->orderItem->sku }} <br>
                        {{ __('Name') }}: <a href="{{ route('product.edit', $toteOrderItem->orderItem->product) }}" target="_blank">{{ $toteOrderItem->orderItem->product->name }}</a>
                    </td>
                    <td>
                        {{ $toteOrderItem->quantity }}
                    </td>
                    <td>
                        {{ user_date_time($toteOrderItem->created_at, true) }}
                    </td>
                    <td>
                        {{ $toteOrderItem->user->contactInformation->name ?? '' }}
                    </td>
                    <td>
                        <a href="{{ route('picking_batch.getItems', ['pickingBatch' => $toteOrderItem->pickingBatchItem->picking_batch_id]) }}"
                           target="_blank"
                        >
                            {{ $toteOrderItem->pickingBatchItem->picking_batch_id }}
                        </a>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>
