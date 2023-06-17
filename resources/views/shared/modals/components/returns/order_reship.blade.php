<div class="modal fade confirm-dialog" id="order-reship-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Reship Items') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body reship-modal-body">
                <form action="{{route('order.reship', ['order'=>$order->id])}}" method="post">
                    @csrf
                    <div class="row w-100 pb-2">
                        <div class="col-lg-6 col-sm-12 col-md-12 col-xl-6">
                            <div class="row w-100">
                                <div class="col-3 text-right pt-2">
                                    <strong>{{ __('Order Status') }}:</strong>
                                </div>
                                <div class="col-9 text-left">
                                    @include('shared.forms.select', [
                                       'name' => 'reship_order_status_id',
                                       'containerClass' => 'mx-2',
                                       'label' => '',
                                       'error' => ! empty($errors->get('order_status_id')) ? $errors->first('order_status_id') : false,
                                       'value' => '',
                                       'options' => [' ' => __("Don't change")] + $orderStatuses->pluck('name', 'id')->toArray()
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-md-12 col-xl-6 pt-2">
                            <input type="checkbox" name="operator_hold" value="1"/> {{__("Place order on Operator Hold after it's reshipped")}}
                        </div>
                    </div>
                    <div class="row w-100">
                        <table class="col-12 table align-items-center table-flush">
                            <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Qt. Ordered') }}</th>
                                <th scope="col">{{ __('Qt. Shipped') }}</th>
                                <th scope="col">{{ __('Qt. Re-Ship') }}</th>
                                <th scope="col">{{ __('Qt. Pending') }}</th>
                                <th scope="col">{{ __('Available') }}</th>
                                <th scope="col">{{ __('Item Price') . ' (' . ($sessionCustomer->currency ?? '') . ')' }}</th>
                                <th scope="col">{{ __('Total Price') . ' (' . ($sessionCustomer->currency ?? '') . ')' }}</th>
                                <th scope="col">{{ __('Add To Inv') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count( $order->orderItems ?? [] ) > 0)
                                @foreach($order->orderItems as $key => $orderItem)
                                    @if($orderItem['quantity_shipped'] > 0)
                                        <tr class="productRow" data-index="{{ $key }}">
                                            <td>
                                                <input name="order_items[{{$key}}][order_item_id]" class="reship_checkbox" type="checkbox" value="{{$orderItem->id}}"/>
                                            </td>
                                            <td>
                                                {{ __('SKU') }}: {!! $orderItem['sku'] !!} <br>
                                                {{ __('Name') }}: <a href="{{ route('product.edit', $orderItem['product']) }}" target="_blank">{!! $orderItem['name'] !!}</a>
                                            </td>
                                            <td>{{ $orderItem['quantity'] ?? 0  }}</td>
                                            <td>{{ $orderItem['quantity_shipped'] ?? 0  }}</td>
                                            <td>
                                                <input name="order_items[{{$key}}][quantity]" class="form-control font-weight-600 px-2 py-1" type="number" value="{{ $orderItem['quantity_shipped'] ?? 0 }}" />
                                            </td>
                                            <td>{{ $orderItem['quantity_pending'] ?? 0  }}</td>
                                            <td>{{ $orderItem['quantity_allocated'] ?? 0 }}</td>
                                            <td>{{ number_format($orderItem['price'], 2) }}</td>
                                            <td>{{ number_format($orderItem['price'] * $orderItem['quantity'], 2) }}</td>
                                            <td>
                                                <input name="order_items[{{$key}}][add_inventory]" type="checkbox" value="{{$orderItem->id}}" value="0"/>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            <tr>
                                <td colspan="10" class="text-right">
                                    <button type="button" class="reship_check_all btn bg-white borderOrange text-logoOrange mx-auto px-5 text-white">{{ __('Check All') }}</button>
                                    <button type="button" data-dismiss="modal"  class="btn bg-white borderOrange text-logoOrange mx-auto px-5 text-white">{{ __('Close') }}</button>
                                    <input class="btn bg-logoOrange mx-auto px-5 text-white reship_submit" type="submit" disabled value="{{__('Reship')}}" />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" value="{{$order->customer->id}}" name="customer_id">
                </form>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
