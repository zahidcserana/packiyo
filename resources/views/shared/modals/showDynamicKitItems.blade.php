<div class="modal-content" id="showDynamicKitItems">
    <div class="modal-header px-0">
        <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
            <h6 class="modal-title text-black text-left"
                id="modal-title-notification">{{ __('Order Item Kits') }}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="text-black">&times;</span>
            </button>
        </div>
    </div>
    <div class="modal-body">
        <table class="col-12 table align-items-center table-flush">
            <thead>
                <tr>
                    <th scope="col">{{ __('Image') }}</th>
                    <th scope="col">{{ __('Product') }}</th>
                    <th scope="col">{{ __('Unit Price') }}</th>
                    <th scope="col">{{ __('Quantity') }}</th>
                    <th scope="col">{{ __('Available') }}</th>
                    <th scope="col">{{ __('Allocated') }}</th>
                    <th scope="col">{{ __('Backordered') }}</th>
                    <th scope="col">{{ __('On Hand') }}</th>
                </tr>
            </thead>
            <tbody id="item_container">
                @foreach( $orderItem->product->kitItems as $key => $kitItem )
                    <tr class="productRow parentProductRow" data-index="{{ $key }}">
                        <td>
                            @if (! empty($kitItem->productImages[0]))
                                <img src="{{ $kitItem->productImages[0]->source }}" alt="">
                                <input type="hidden" name="order_items[{{ $key }}][img]" value="{{ $kitItem->productImages[0]->source }}">
                            @else
                                <img src="{{ asset('img/no-image.png') }}" alt="No image">
                            @endif
                        </td>
                        <td>
                            SKU: {!! $kitItem['sku'] !!} <br>
                            Name: {!! $kitItem['name'] !!}
                        </td>
                        <td class="product-price">
                            <span class="price-value">{{ $kitItem['price'] }}</span>
                        </td>
                        <td>
                            <span class="price-value">{{ $kitItem->pivot['quantity'] }}</span>
                        </td>
                        <td>
                            <span class="price-value">{{ $kitItem['quantity_available'] }}</span>
                        </td>
                        <td>
                            <span class="price-value">{{ $kitItem['quantity_allocated'] }}</span>
                        </td>
                        <td>
                            <span class="price-value">{{ $kitItem['quantity_backordered'] }}</span>
                        </td>
                        <td>
                            <span class="price-value">{{ $kitItem['quantity_on_hand'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
