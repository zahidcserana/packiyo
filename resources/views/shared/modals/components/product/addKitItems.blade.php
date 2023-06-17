<div class="modal fade confirm-dialog" id="addKitItems" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white p-3">
            @include('shared.forms.kitProductInput', [
                    'url' => route('product.filterKitProducts',[ 'customer' => $product->customer ] ),
                    'className' => 'ajax-user-input sendFilteredRequest',
                    'placeholder' => 'Search',
                    'label1' => ('Product'),
                    'label2' => __('Quantity'),
                    'visible' => ! empty($product) &&  count($product->kitItems),
                    'defaults' => ''
                ])

            <button type="button" class="btn bg-logoOrange text-white my-4 font-weight-700 mt-3" id="save-added-kit-items">Save</button>
        </div>
    </div>
</div>
