<form method="post" action="{{ route('location.store') }}" autocomplete="off" class="locationForm modal-content">
    @csrf
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Create location') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body text-center py-3 overflow-auto" id="modalBody">
        <div class="justify-content-md-between inputs-container">
            @if(!isset($sessionCustomer))
                <div class="d-sm-flex">
                    <div class="w-50">
                        <div class="searchSelect">
                            <div class="form-group mb-0 mx-2 text-left mb-3" readonly>
                                <label data-id="customer_id" class="form-control-label text-neutral-text-gray font-weight-600 font-xs">Customer</label>
                                <select
                                    name="customer_id"
                                    id="customer_id"
                                    data-placeholder="Select customer"
                                >
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
            @endif
            <div class="d-sm-flex">
                <div class="w-50">
                    <div class="searchSelect">
                        <div class="form-group mb-0 mx-2 text-left mb-3" readonly>
                            <label data-id="warehouse_id" class="form-control-label text-neutral-text-gray font-weight-600 font-xs">Warehouse</label>
                            <select
                                name="warehouse_id"
                                id="warehouse_id"
                                data-placeholder="Select a warehouse"
                            >
                            </select>
                        </div>
                    </div>
                </div>
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="name"
                               class="text-neutral-text-gray font-weight-600 font-xs">
                            {{ __('Name') }}
                        </label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Name') }}"
                                type="text"
                                name="name"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-sm-flex">
                <div class="w-50">
                    <div class="searchSelect">
                        <div class="form-group mb-0 mx-2 text-left mb-3" readonly>
                            <label data-id="location_type_id" class="form-control-label text-neutral-text-gray font-weight-600 font-xs">Location Type</label>
                            <select
                                name="location_type_id"
                                id="location_type_id"
                                class="location_type_id"
                                data-placeholder="Choose a location type"
                            >
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100">
                <div class="d-flex mt-3 custom-form-checkbox mx-2">
                    @include('shared.forms.checkbox', [
                           'name' => 'pickable',
                           'label' => __('Pickable'),
                           'checked' => 0,
                        ])
                    @include('shared.forms.checkbox', [
                           'name' => 'disabled_on_picking_app',
                           'label' => __('Disabled on picking app'),
                           'containerClass' => 'ml-2',
                           'checked' => 0,
                        ])
                    @include('shared.forms.checkbox', [
                           'name' => 'sellable',
                           'label' => __('Sellable'),
                           'containerClass' => 'ml-2',
                           'checked' => 0,
                        ])
                </div>
            </div>
            <div class="w-auto">
                <div class="table-responsive table-overflow p-0">
                    <table class="col-12 table align-items-center table-flush">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Quantity on hand') }}</th>
                                <th scope="col">{{ __('Delete') }}</th>
                            </tr>
                        </thead>
                        <tbody id="item_container">
                        <label data-id="location_product.0.product_id"></label>
                        <tr class="order-item-fields">
                                <td style="">
                                    <div class="searchSelect">
                                        <div class="form-group mb-0 mx-2 text-left mb-3" readonly>
                                            <select
                                                name="location_product[0][product_id]"
                                                class="product_location_id"
                                                data-placeholder="Search for a product to add"
                                            >
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0 mx-2 text-left mb-3">
                                        <div
                                            class="input-group input-group-alternative input-group-merge tableSearch">
                                            <input
                                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                                type="number"
                                                name="location_product[0][quantity_on_hand]"
                                                value="0"
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="table-icon-button delete-item">
                                        <i class="picon-trash-filled" title="Delete"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button id="add_item" type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700">{{ __('Add more items') }}</button>
                </div>
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
        const locationCreateModal = $("#locationCreateModal")

        let warehouseSelect = $('#warehouse_id')
        let locationTypeSelect = $('.location_type_id')
        let productLocationSelect = $('.product_location_id')
        let customerSelect = $('#customer_id')

        productLocationSelect.select2({
            dropdownParent: locationCreateModal
        });

        customerSelect.select2({
            ajax: {
                url: "{{ route('user.getCustomers') }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        warehouseSelect.select2({
            ajax: {
                url: "{{ route('purchase_order.filterWarehouses', ['customer' => isset($sessionCustomer) ? $sessionCustomer->id : null]) }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        productLocationSelect.select2({
            ajax: {
                url: "{{ route('location.filterProducts', ['location' => null]) }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        locationTypeSelect.select2({
            ajax: {
                url: "{{ route('location.types', ['customer' => isset($sessionCustomer) ? $sessionCustomer->id : null]) }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        $('#add_item').click(function (event) {
            event.preventDefault();
            let lastOrderItemFields = $('.order-item-fields:not(.order-item-deleted):last');

            lastOrderItemFields.find('select').select2('destroy');

            let orderItemFieldsHtml = lastOrderItemFields[0].outerHTML;
            let index = orderItemFieldsHtml.match(/\[(\d+?)\]/);
            let orderItemFields = $(orderItemFieldsHtml.replace(/\[\d+?\]/g, '[' + (parseInt(index[1]) + 1) + ']'));

            $('#item_container').append(orderItemFields);
            $('.order-item-fields:last').find('input[type=hidden]').remove();
            $('.order-item-fields:last').show();

            lastOrderItemFields.find('select').select2();
            let orderItemSelect2 = orderItemFields.find('select').select2();

            orderItemSelect2.empty().trigger('change')

            const addItemButton = document.querySelector('#add_item')
            addItemButton.scrollIntoView()

            checkDeleteButton();
        });

        $('.modal-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.locationForm');
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
                    $('#locationCreateModal').modal('toggle');

                    toastr.success(data.message)

                    dtInstances['#locations-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#locationCreateModal'),
                        response
                    )
                }
            });
        });

        customerSelect.on('change', function () {
            let customerId = customerSelect.val()

            warehouseSelect.empty()
            locationTypeSelect.empty()
            productLocationSelect.empty()

            if (customerId) {
                warehouseSelect.select2({
                    ajax: {
                        url: "purchase_orders/filterWarehouses/" + customerId,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                })

                locationTypeSelect.select2({
                    ajax: {
                        url: "location/types/filter/" + customerId,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                })

                productLocationSelect.select2({
                    ajax: {
                        url: "location/filterProducts?customer=" + customerId,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                })
            }
        }).trigger('change');
    });
</script>
