<form method="post" action="{{ route('warehouses.update', [ 'warehouse' => $warehouse, 'id' => $warehouse->id ]) }}" autocomplete="off" class="warehouseForm modal-content">
    @csrf
    {{ method_field('PUT') }}
    <input type="hidden" name="customer_id" value="{{ $warehouse->customer_id }}">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Edit warehouse') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        <div class="d-sm-flex justify-content-md-between inputs-container">
            <div class="w-50">
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.name"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Name') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Name') }}"
                            type="text"
                            name="contact_information[name]"
                            value="{{ $warehouse->contactInformation->name ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.company_name"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Company Name') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Company Name') }}"
                            type="text"
                            name="contact_information[company_name]"
                            value="{{ $warehouse->contactInformation->company_name ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.company_number"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Company number') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Company number') }}"
                            type="text"
                            name="contact_information[company_number]"
                            value="{{ $warehouse->contactInformation->company_number ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.address"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Address') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Address') }}"
                            type="text"
                            name="contact_information[address]"
                            value="{{ $warehouse->contactInformation->address ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.address2"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Address 2') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Address 2') }}"
                            type="text"
                            name="contact_information[address2]"
                            value="{{ $warehouse->contactInformation->address2 ?? '' }}"
                        >
                    </div>
                </div>
            </div>
            <div class="w-50">
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.zip"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('ZIP') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('ZIP') }}"
                            type="text"
                            name="contact_information[zip]"
                            value="{{ $warehouse->contactInformation->zip ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.city"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('City') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('City') }}"
                            type="text"
                            name="contact_information[city]"
                            value="{{ $warehouse->contactInformation->city ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.phone"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Phone') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Phone') }}"
                            type="text"
                            name="contact_information[phone]"
                            value="{{ $warehouse->contactInformation->phone ?? '' }}"
                        >
                    </div>
                </div>
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="contact_information.email"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Contact Email') }} </label>
                    <div
                        class="form-group input-group-alternative input-group-merge  ">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Contact Email') }}"
                            type="email"
                            name="contact_information[email]"
                            value="{{ $warehouse->contactInformation->email ?? '' }}"
                        >
                    </div>
                </div>

                @include('shared.forms.countrySelect', [
                    'name' => 'contact_information[country_id]',
                    'containerClass' => 'mb-0 mx-2 text-left mb-3',
                    'value' => $warehouse->contactInformation->country_id ?? ''
                ])
            </div>
        </div>
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
                    $('#warehouseEditModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#warehouses-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#warehouseEditModal'),
                        response
                    )
                }
            });
        });
    });
</script>
