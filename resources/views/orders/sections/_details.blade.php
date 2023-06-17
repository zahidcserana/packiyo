<table class="table table-sm table-borderless table-layout-fixed table-order table-details font-sm">
    <tbody>
        <tr>
            <td class="text-break align-middle">{{ __('Order #') }}</td>
            <td class="text-right">{{ $order->number }}</td>
        </tr>
        <tr>
            <td class="text-break align-middle">{{ __('Order date') }}</td>
            <td class="text-right">{{ $order->ordered_at->format('d.m.Y - H:i') }}</td>
        </tr>
        <tr>
            <td class="text-break align-middle">{{ __('Currency') }}</td>
            <td class="text-right">{{ $currency }}</td>
        </tr>
        <tr>
            <td class="text-break align-middle">{{ __('Status') }}</td>
            <td class="text-right">
                @if($order->getStatusText() === \App\Models\Order::STATUS_FULFILLED)
                    <span>{{ __(\App\Models\Order::STATUS_FULFILLED) }}</span>
                @elseif($order->getStatusText() === \App\Models\Order::STATUS_CANCELLED)
                    <span>{{ __(\App\Models\Order::STATUS_CANCELLED) }}</span>
                @else
                    @include('shared.forms.select', [
                        'name' => 'order_status_id',
                        'label' => '',
                        'className' => 'order_status_id',
                        'placeholder' => __('Select an order status'),
                        'error' => !empty($errors->get('order_status_id')) ? $errors->first('order_status_id') : false,
                        'value' => $order->order_status_id ?? 'pending',
                        'options' => $orderStatuses->pluck('name', 'id')
                    ])
                @endif
            </td>
        </tr>
        <tr>
            <td class="p-4"></td>
            <td class="p-4"></td>
        </tr>
        @if ($order->ready_to_ship && $order->ready_to_pick)
            <tr>
                <td class="text-word-break align-middle" colspan="2">{{ __('This order is ready to ship') }}</td>
            </tr>
        @endif
        @if ($order->orderLock)
            <tr>
                <td class="text-word-break align-middle" colspan="2">{{ __('This order is locked by :name', ['name' => $order->orderLock->user->contactInformation->name]) }}</td>
            </tr>
        @elseif(!($order->ready_to_ship && $order->ready_to_pick) && !($order->fulfilled_at || $order->cancelled_at))
            <tr>
                @if(count($order->notReadyToShipExplanation()) > 0)
                    <td class="text-word-break align-middle" colspan="2">
                        {{ __('This order is not ready to ship because:') }}
                        <ul>
                            @foreach($order->notReadyToShipExplanation() as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    </td>
                @elseif(!is_null($order->notReadyToPickExplanation()))
                        <td class="text-word-break align-middle" colspan="2">
                            {{ __('This order is ready to ship, but not ready to pick because:') }}
                            <ul>
                                <li>{{ $order->notReadyToPickExplanation() }}</li>
                            </ul>
                        </td>
                @endif
            </tr>
        @endif
        @if(!$order->has_holds)
            <tr>
                <td class="text-word-break align-middle" colspan="2">{{ __('No holds on this order') }}</td>
            </tr>
        @endif
        <tr>
            <td class="text-word-break align-middle">{{ __('Hold Until:') }}</td>
            <td>
                @include('shared.forms.input', [
                    'containerClass' => 'd-flex justify-content-end',
                    'name' => 'required_shipping_date_at',
                    'label' => '',
                    'error' => ! empty($errors->get('required_shipping_date_at')) ? $errors->first('required_shipping_date_at') : false,
                    'value' => !empty($order->required_shipping_date_at) ? user_date_time($order->required_shipping_date_at) :  '',
                    'class' => 'dt-daterangepicker text-right'
                ])
            </td>
        </tr>
        <tr>
            <td class="text-word-break align-middle">{{ __('Required ship date:') }}</td>
            <td>
                @include('shared.forms.input', [
                    'containerClass' => 'd-flex justify-content-end',
                    'name' => 'shipping_date_before_at',
                    'label' => '',
                    'error' => ! empty($errors->get('shipping_date_before_at')) ? $errors->first('shipping_date_before_at') : false,
                    'value' => !empty($order->shipping_date_before_at) ? user_date_time($order->shipping_date_before_at) :  '',
                    'class' => 'dt-daterangepicker text-right'
                ])
            </td>
        </tr>
        @if($order->custom_invoice_url)
            <tr>
                <td></td>
                <td class="text-right">
                    <a href="{{ $order->custom_invoice_url }}" target="_blank" class="btn bg-logoOrange text-white mx-auto px-3 py-2 font-weight-700 border-8">
                        {{ __('Invoice') }}
                    </a>
                </td>
            </tr>
        @endif
    </tbody>
</table>
