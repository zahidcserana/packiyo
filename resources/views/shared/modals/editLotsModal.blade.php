<div class="modal fade confirm-dialog" id="editLotsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-notification">{{ __('Edit Lot') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-white text-center py-3">
                <div class="row">
                    <div class="col-6">
                        @include('shared.forms.new.ajaxSelect', [
                            'url' => route('supplier.filterByProduct'),
                            'name' => 'supplier_id',
                            'className' => 'ajax-user-input supplier_id',
                            'placeholder' => __('Select supplier'),
                            'label' => __('Supplier'),
                            'fixRouteAfter' => '.ajax-user-input.supplier_id',
                            'id' => 'lot_supplier_id'
                        ])
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                @include('shared.forms.input', [
                                    'name' => 'lot',
                                    'id' => 'lot_name',
                                    'type' => 'text',
                                    'label' => __('Lot Name')
                                ])
                            </div>
                            <div class="col-12">
                                @include('shared.forms.input', [
                                    'name' => 'lot_expiration',
                                    'id' => 'expiration_date',
                                    'type' => 'date',
                                    'label' => __('Expiration date')
                                ])
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="reset" class="btn bg-white text-black mx-auto px-5 confirm-button" id="reset_lot_button">{{ __('Reset') }}</button>
                <button type="button" class="btn bg-logoOrange text-white mx-auto px-5 confirm-button" id="set_lot_button">{{ __('Save lot') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const editLotsModal = $("#editLotsModal");

        editLotsModal.find('.ajax-user-input').select2({
            dropdownParent: editLotsModal
        })
    })
</script>
