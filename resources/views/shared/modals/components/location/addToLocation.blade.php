<div class="modal fade confirm-dialog" id="add-new-location-modal" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" action="#" autocomplete="off" class="addToLocationForm modal-content" id="add-to-location-form">
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Add product to location') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-3 overflow-auto">
                    <div class="justify-content-md-between inputs-container">
                        <div class="row w-100">
                            <table id="add-new-location-table" class="table align-items-center col-12"
                                   data-product="{{ $product->id }}"
                                   data-url="{{ route('location.filterLocations') . '?product_id=' . $product->id }}"
                                   data-lot-url="{{ route('lot.filterLots') . '?product_id=' . $product->id }}"
                                   data-placeholder="{{  __('Search for a location') }}"
                                   data-lot-placeholder="{{  __('Search for a lot') }}"
                            >
                                <thead>
                                <th>{{__('Location')}}</th>
                                <th class="w-25">{{__('Quantity')}}</th>
                                @if($product->lot_tracking == 1)
                                    <th class="w-50">{{__('Lot')}}</th>
                                @endif
                                <th>&nbsp;</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="row w-100 text-left">
                            <button type="button" id="add-another-location" class="btn bg-logoOrange mx-auto px-5 text-white">+</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-logoOrange mx-auto px-5 text-white" id="add-to-location-form-cancel">{{ __('Cancel') }}</button>
                    <button type="button" class="btn bg-logoOrange mx-auto px-5 text-white" id="add-to-location-form-save">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
