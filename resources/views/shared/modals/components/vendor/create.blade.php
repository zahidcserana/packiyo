<div class="modal fade confirm-dialog" id="supplierCreateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form method="post" action="{{ route('supplier.store') }}" autocomplete="off" data-type="POST" id="supplier-create-form" enctype="multipart/form-data"
              class="modal-content supplierForm">
            @csrf
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Create vendor') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-center py-3 overflow-auto">
                @if(!isset($sessionCustomer))
                    <div class="searchSelect">
                        @include('shared.forms.new.ajaxSelect', [
                        'url' => route('user.getCustomers'),
                        'name' => 'customer_id',
                        'className' => 'ajax-user-input customer_id customer-create',
                        'placeholder' => __('Select customer'),
                        'label' => __('Customer'),
                        'default' => [
                            'id' => old('customer_id'),
                            'text' => ''
                        ],
                        'fixRouteAfter' => '.ajax-user-input.customer_id'
                    ])
                    </div>
                @else
                    <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
                @endif
                @include('supplier.supplierInformationFields')
            </div>
            <div class="modal-footer">
                <button type="submit"
                        class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-create-submit-button"
                        id="submit-button">{{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
