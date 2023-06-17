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
                    <h6 class="heading-small text-muted my-3">{{ __('Add order holds') }}</h6>
                    <div class="d-flex flex-wrap">
                        @include('shared.forms.checkbox', [
                            'name' => 'add_operator_hold',
                            'label' => __('Operator hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'add_payment_hold',
                            'label' => __('Payment hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'add_address_hold',
                            'label' => __('Address hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'add_fraud_hold',
                            'label' => __('Fraud hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'add_allocation_hold',
                            'label' => __('Allocation hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                    </div>
                    <h6 class="heading-small text-muted my-3">{{ __('Remove order holds') }}</h6>
                    <div class="d-flex flex-wrap">
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_operator_hold',
                            'label' => __('Operator hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_payment_hold',
                            'label' => __('Payment hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_address_hold',
                            'label' => __('Address hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_fraud_hold',
                            'label' => __('Fraud hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_allocation_hold',
                            'label' => __('Allocation hold'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'remove_all_holds',
                            'label' => __('Remove all holds'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                    </div>
                    <h6 class="heading-small text-muted my-3">{{ __('Add order notes') }}</h6>
                    <div class="row">
                        <div class="col-6 col-md-3">
                            @include('shared.forms.textarea', [
                               'name' => 'packing_note',
                               'label' => __('Note to packer')
                            ])
                        </div>
                        <div class="col-6 col-md-3">
                            @include('shared.forms.textarea', [
                               'name' => 'slip_note',
                               'label' => __('Slip note')
                            ])
                        </div>
                        <div class="col-6 col-md-3">
                            @include('shared.forms.textarea', [
                               'name' => 'gift_note',
                               'label' => __('Gift note')
                            ])
                        </div>
                        <div class="col-6 col-md-3">
                            @include('shared.forms.textarea', [
                               'name' => 'internal_note',
                               'label' => __('Internal note')
                            ])
                        </div>
                    </div>
                    <div class="d-flex flex-wrap">
                        @include('shared.forms.checkbox', [
                            'name' => 'allow_partial',
                            'label' => __('Allow partial'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                        @include('shared.forms.checkbox', [
                            'name' => 'priority',
                            'label' => __('Priority'),
                            'containerClass' => 'mx-2',
                            'checked' => false
                        ])
                    </div>
                    <div class="row">
                        @include('shared.forms.countrySelect', [
                           'containerClass' => 'col-12 col-md-6',
                           'name' => 'country_id',
                           'allowClear' => true
                       ])
                        @if(isset($sessionCustomer))
                            @include('shared.forms.new.ajaxSelect', [
                                'url' => route('shipping_method_mapping.filterShippingMethods'),
                                'name' => 'shipping_method_id',
                                'containerClass' => 'col-12 col-md-6',
                                'className' => 'ajax-user-input shipping_method_id',
                                'placeholder' => __('Select a shipment method'),
                                'label' => __('Shipping method'),
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
                    <button type="button" id="cancel-bulk-order" class="btn mx-auto px-5 my-1 text-black" hidden>{{ __('Cancel all selected orders') }}</button>
                    <button type="button" id="mark-as-fulfilled" class="btn mx-auto px-5 my-1 text-black" hidden>{{ __('Mark all selected orders as fulfilled') }}</button>
                    <button type="submit" id="submit-bulk-order-edit" class="btn bg-logoOrange mx-auto px-5 my-1 text-white">{{ __('Save on all') }} <span id="number-of-selected-items"></span> <span id="item-type"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>
