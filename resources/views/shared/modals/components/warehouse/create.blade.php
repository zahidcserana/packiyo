<form method="post" action="{{ route('warehouses.store') }}" autocomplete="off" class="warehouseForm modal-content">
    @csrf
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Create warehouse') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body py-3 overflow-auto">
        @if(!isset($sessionCustomer))
            <div class="searchSelect">
                @include('shared.forms.new.ajaxSelect', [
                'url' => route('user.getCustomers'),
                'name' => 'customer_id',
                'className' => 'ajax-user-input customer_id',
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

        @include('shared.forms.contactInformationFields', [
            'name' => 'contact_information',
        ])

    </div>
    <div class="modal-footer">
        <button type="submit"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-submit-button"
                id="submit-button">{{ __('Save') }}
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.modal-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.warehouseForm');
            let form = _form[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#warehouseCreateModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#warehouses-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#warehouseCreateModal'),
                        response
                    )
                }
            });
        });
    });
</script>
