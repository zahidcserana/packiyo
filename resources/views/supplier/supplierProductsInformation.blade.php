<div class="w-100">
    <h4 class="form-control-label text-neutral-text-gray">{{ __('Vendor Products') }}</h4>
    <div class="table-responsive">
        <table class="table col-12 text-center no-footer" id="relatedProducts">
            <thead>
            <tr>
                <th scope="col" class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Name') }}</th>
                <th scope="col" class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Sku') }}</th>
                <th scope="col" class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Price') }}</th>
                <th scope="col" class="border-top-0 text-neutral-text-gray font-weight-600 font-xs">{{ __('Quantity') }}</th>
            </tr>
            </thead>
            <tbody id="product_container">
            @if(! empty($products) && count($products))
                @foreach($products as $product)
                    <tr>
                        <td class="py-4 text-black font-weight-600 font-sm">{{ $product->name }}</td>
                        <td class="py-4 text-black font-weight-600 font-sm">{{ $product->sku }}</td>
                        <td class="py-4 text-black font-weight-600 font-sm">{{ $product->price }}</td>
                        <td class="py-4 text-black font-weight-600 font-sm">{{ $product->quantity_on_hand }}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @if( count($products) === 0)
            <div class="empty-products-table">
                {{ __('There are no products related to this supplier yet') }}
            </div>
        @endif
    </div>
</div>
