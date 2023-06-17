<div class="modal fade confirm-dialog" id="transfer-modal" data-positioned="true" role="dialog">
    <div class="modal-dialog modal-md mx-0" role="document">
        <div class="modal-content bg-white">
            <form method="post" action="{{ route('product.transfer', ['product' => $product]) }}" autocomplete="off" class="transferLocationForm modal-content">
                @csrf
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Transfer product to location') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-3 overflow-auto">
                    <div class="justify-content-md-between inputs-container">
                        <div class="w-100">
                            <div class="searchSelect">
                                <input type="hidden" name="from_location_id" id="from_location_id">
                                @include('shared.forms.ajaxSelect', [
                                    'url' => route('product.filterLocations', ['product' => $product]),
                                    'name' => 'to_location_id',
                                    'className' => 'ajax-user-input',
                                    'placeholder' => __('Search for a location'),
                                    'label' => __('Transfer to')
                                ])
                                @include('shared.forms.input', [
                                    'name' => 'quantity',
                                    'type' => 'number',
                                    'label' => __('Quantity'),
                                    'value' => 1
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-logoOrange mx-auto px-5 text-white submitTransfer">{{ __('Transfer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
