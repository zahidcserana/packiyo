<div class="modal fade confirm-dialog" id="select-drop-point-modal" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <div class="modal-header border-bottom mx-4 px-0">
                <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Select drop point for shipment') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true" class="text-black">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-3 overflow-auto">
                <div class="searchSelect">
                    @include('shared.forms.new.ajaxSelect', [
                    'url' => route('shipping_method.getDropPoints'),
                    'name' => 'drop_point_id',
                    'className' => 'ajax-user-input drop_point_id',
                    'placeholder' => __('Select a drop point'),
                    'searchOnClick' => true,
                    'label' => '',
                    'default' => [
                        'id' => old('drop_point_id'),
                        'text' => ''
                    ]
                ])
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="submit-drop-point" class="btn bg-logoOrange mx-auto px-5 text-white">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
