<div class="modal fade confirm-dialog" id="bulk-edit-modal" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" autocomplete="off" class="modal-content" id="bulk-edit-form">
                @csrf
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Bulk Edit') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="ids" id="model-ids">
                <div class="modal-body py-3 overflow-auto">
                    @include('shared.forms.editSelectTag', [
                        'containerClass' => 'mb-2',
                        'labelClass' => '',
                        'selectClass' => 'select-ajax-tags bulk-edit-tags',
                        'selectId' => '',
                        'name' => 'add_tags[]',
                        'label' => __('Add tags'),
                        'minimumInputLength' => 3,
                        'dropDownParent' => '#bulk-edit-modal',
                    ])
                    @include('shared.forms.editSelectTag', [
                        'containerClass' => 'mb-2',
                        'labelClass' => '',
                        'selectClass' => 'select-ajax-tags bulk-edit-tags',
                        'selectId' => '',
                        'name' => 'remove_tags[]',
                        'label' => __('Remove tags'),
                        'minimumInputLength' => 3,
                        'dropDownParent' => '#bulk-edit-modal',
                    ])
                    <div class="d-flex flex-wrap my-3">
                        @include('shared.forms.checkbox', [
                            'name' => 'lot_tracking',
                            'label' => __('Track lots and expiration'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'has_serial_number',
                            'label' => __('Needs serial number'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'priority_counting_requested_at',
                            'label' => __('Priority counting'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_empty_locations',
                            'label' => __('Delete all empty locations'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                    </div>
                    <div class="row">
                        @include('shared.forms.input', [
                            'containerClass' => 'col-12 col-md-6',
                            'name' => 'hs_code',
                            'label' => __('HS Code')
                        ])

                        @include('shared.forms.input', [
                            'containerClass' => 'col-12 col-md-6',
                            'name' => 'reorder_threshold',
                            'label' => __('Reorder Threshold'),
                            'type' => 'number'
                        ])

                        @include('shared.forms.input', [
                            'containerClass' => 'col-12 col-md-6',
                            'name' => 'quantity_reorder',
                            'label' => __('Reorder Quantity'),
                            'type' => 'number'
                        ])

                        @include('shared.forms.textarea', [
                            'containerClass' => 'col-12 col-md-6',
                            'name' => 'notes',
                            'label' => __('Notes')
                        ])
                    </div>
                    <div class="row">
                        @include('shared.forms.countrySelect', [
                            'containerClass' => 'col-12 col-md-6',
                            'label' => 'Country of origin',
                            'name' => 'country_id',
                            'allowClear' => true
                        ])
                        @if(isset($sessionCustomer))
                            @include('shared.forms.new.ajaxSelect', [
                                'url' => route('product.filterSuppliers'),
                                'name' => 'vendor_id',
                                'containerClass' => 'col-12 col-md-6',
                                'className' => 'ajax-user-input vendor_id',
                                'placeholder' => __('Add vendor'),
                                'label' => __('Vendor'),
                                'default' => [
                                    'id' => '',
                                    'text' => ''
                                ],
                                'fixRouteAfter' => '.ajax-user-input.customer_id'
                            ])
                        @endif
                    </div>
                </div>
                <div class="modal-footer row">
                    <button type="button" id="products-bulk-delete" class="btn mx-auto px-5 my-1 text-black" hidden>{{ __('Delete all selected products') }}</button>
                    <button type="button" id="products-bulk-recover" class="btn mx-auto px-5 my-1 text-black" hidden>{{ __('Recover all selected products') }}</button>
                    <button type="submit" id="submit-bulk-product-edit" class="btn bg-logoOrange mx-auto px-5 my-1 text-white">{{ __('Save on all') }} <span id="number-of-selected-items"></span> <span id="item-type"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
