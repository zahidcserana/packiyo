<form
    method="post"
    action="{{ route('location.update', ['location' => $location, 'id' => $location->id, 'warehouse_id' => $location->warehouse->id]) }}"
    autocomplete="off"
    data-location-modal-route="{{ route('location.getLocationModal', [$location->warehouse, $location]) }}"
    class="locationForm modal-content">
    @csrf
    {{ method_field('PUT') }}
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Edit location') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        <div class="row inputs-container">
            <div class="col-lg-6">
                <div class="form-group mb-0 mx-2 text-left mb-3">
                    <label for=""
                           data-id="name"
                           class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Name') }} </label>
                    <div
                        class="input-group input-group-alternative input-group-merge">
                        <input
                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                            placeholder="{{ __('Name') }}"
                            type="text"
                            name="name"
                            value="{{ $location->name }}"
                        >
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="searchSelect">
                    <div class="form-group mb-0 mx-2 text-left mb-3" readonly>
                        <label data-id="location_type_id" class="form-control-label text-neutral-text-gray font-weight-600 font-xs">Location Type</label>
                        <select
                            name="location_type_id"
                            id="location_type_id"
                            data-placeholder="Choose a location type"
                        >
                            @if(!is_null($location->locationType))
                                <option value="{{ $location->locationType->id }}" selected>{{ $location->locationType->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="w-50">
                    @if(!isset($sessionCustomer) && !isset($location->warehouse->customer_id))
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
                    @elseif(isset($location->warehouse->customer_id))
                        <input type="hidden" name="customer_id" value="{{ $location->warehouse->customer_id }}" class="customer_id" />
                    @else
                        <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
                    @endif
                </div>
            </div>
            <div class="col-lg-12">
                <div class="d-flex mt-3 custom-form-checkbox mx-2">
                    @include('shared.forms.checkbox', [
                           'name' => 'pickable',
                           'label' => __('Pickable'),
                           'checked' => $location->pickable ? 1 : 0,
                        ])
                    @include('shared.forms.checkbox', [
                           'name' => 'disabled_on_picking_app',
                           'label' => __('Disabled on picking app'),
                           'containerClass' => 'ml-2',
                           'checked' => $location->disabled_on_picking_app ? 1 : 0,
                        ])
                    @include('shared.forms.checkbox', [
                          'name' => 'sellable',
                          'label' => __('Sellable'),
                          'containerClass' => 'ml-2',
                          'checked' => $location->sellable ? 1 : 0,
                       ])
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive p-4">
                    <table class="col-12 table align-items-center table-flush">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Quantity on hand') }}</th>
                                <th scope="col">{{ __('Delete') }}</th>
                            </tr>
                        </thead>
                        <tbody id="item-container">
                        <label data-id="location_product.0.product_id"></label>
                        @if(count($location->products) === 0)
                            <tr class="order-item-fields">
                                <td style="">
                                    <div class="searchSelect">
                                        @include('shared.forms.new.ajaxSelect', [
                                        'url' => route('location.filterProducts', ['location' => null]),
                                        'name' => 'location_product[0][product_id]',
                                        'className' => 'ajax-user-input product-location-id',
                                        'placeholder' => __('Search for a product to add'),
                                        'labelClass' => 'd-block',
                                        'label' => '',
                                        'default' => [
                                            'id' => '',
                                            'text' => old('product_id') ?? ''
                                        ],
                                    ])
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group text-left">
                                        <label for=""
                                               data-id="location_product[0][quantity_on_hand]"
                                               class="text-neutral-text-gray font-weight-600 font-xs">
                                        </label>
                                        <div
                                            class="input-group input-group-alternative input-group-merge tableSearch">
                                            <input
                                                class="form-control font-weight-600 text-neutral-gray h-auto reset-on-delete"
                                                type="number"
                                                name="location_product[0][quantity_on_hand]"
                                                value="0"
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="table-icon-button delete-location">
                                        <i class="picon-trash-filled" title="Delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                        @foreach($locationProducts as $product)
                            <input type="hidden" name="location_product[{{$product->id}}][location_product_id]" value="{{$product->id}}">
                            <tr class="order-item-fields">
                                <td style="white-space: nowrap">
                                    <div class="searchSelect">
                                    <input id="selected_product" value="{{$product->product_id}}" type="hidden">
                                        @include('shared.forms.new.ajaxSelect', [
                                        'url' => route('location.filterProducts', ['location' => $location]),
                                        'name' => 'location_product[' . $product->id . '][product_id]',
                                        'className' => 'ajax-user-input product-location-id',
                                        'placeholder' => __('Search for a product to add'),
                                        'labelClass' => 'd-block',
                                        'label' => '',
                                        'default' => [
                                            'id' => $product->product_id,
                                            'text' => 'SKU: ' . $product->product['sku'] . ', NAME: ' . $product->product['name']
                                        ],
                                    ])
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group text-left">
                                        <label for=""
                                               data-id="location_product[{{ $product->id }}][quantity_on_hand]"
                                               class="text-neutral-text-gray font-weight-600 font-xs">
                                        </label>
                                        <div
                                            class="input-group input-group-alternative input-group-merge tableSearch">
                                            <input
                                                class="form-control font-weight-600 text-neutral-gray h-auto reset-on-delete"
                                                type="number"
                                                name="location_product[{{ $product->id }}][quantity_on_hand]"
                                                value="{{ $product->quantity_on_hand }}"
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
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="custom-pagination py-4">
            {!! $locationProducts->links('pagination.default') !!}
        </div>
    </div>
    <div class="modal-footer">
        <button id="add-item-to-edit" type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700">
            {{ __('Add more items') }}
        </button>
        <button type="submit"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 modal-submit-button"
                id="submit-button">{{ __('Save') }}
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('.product-location-id').select2({
            dropdownParent: $("#locationEditModal")
        });

        let locationTypeSelect = $('#location_type_id')
        let customerSelect = $('#customer_id')

        locationTypeSelect.select2({
            dropdownParent: $("#locationEditModal"),
            ajax: {
                url: "{{ route('location.types', ['customer' => isset($sessionCustomer) ? $sessionCustomer->id : null]) }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        customerSelect.select2({
            dropdownParent: $("#locationEditModal"),
            ajax: {
                url: "{{ route('user.getCustomers') }}",
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        customerSelect.on('change', function () {
            let customerId = customerSelect.val()

            locationTypeSelect.empty()

            if (customerId) {
                locationTypeSelect.select2({
                    dropdownParent: $("#locationEditModal"),
                    ajax: {
                        url: "location/types/filter/" + customerId,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                })
            }
        }).trigger('change');

        $(document).on('click', '.delete-location', function (event) {
            $(this).parent().parent().find('.reset-on-delete').val(0);
            $(this).parent().parent().hide().addClass('order-item-deleted');

            if ($(this).data('id')) {
                let productID = $(this).data('id')
                $('.locationForm').append('<input type="hidden" name="location_product[' + productID + '][delete]" value="1" />');
            }

            event.preventDefault();
        });

        const orderItemField = $('.order-item-fields:not(.order-item-deleted):last')

        $('#add-item-to-edit').click(function (event) {
            event.preventDefault();
            let lastOrderItemFields = $('.order-item-fields:not(.order-item-deleted):last');
            $(".inputs-container").animate({
                scrollTop: $('.inputs-container')[0].scrollHeight + $('.inputs-container')[0].clientHeight
            }, 50);

            if (typeof lastOrderItemFields[0] === 'undefined') {
                lastOrderItemFields = orderItemField
            }

            lastOrderItemFields.find('select').select2('destroy');

            let orderItemFieldsHtml = lastOrderItemFields[0].outerHTML;
            let index = orderItemFieldsHtml.match(/\[(\d+?)\]/);
            let orderItemFields = $(orderItemFieldsHtml.replace(/\[\d+?\]/g, '[' + (parseInt(index[1]) + 1) + ']'));

            $('#item-container').append(orderItemFields);
            $('.order-item-fields:last').find('input[type=hidden]').remove();
            $('.order-item-fields:last').show();

            lastOrderItemFields.find('select').select2();
            let orderItemSelect2 = orderItemFields.find('select').select2();

            orderItemSelect2.empty().trigger('change')

            $('.product-location-id').select2({
                dropdownParent: $("#locationEditModal")
            });

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
                    $('#locationEditModal').modal('toggle');

                    toastr.success(data.message)

                    dtInstances['#locations-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#locationEditModal'),
                        response
                    )
                }
            });
        });

        $(document).on('click', '.pagination a', function(event){
            if(event.isDefaultPrevented()) return;
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $('#locationEditModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            fetch_next_location_product_page(page);
        });

        function fetch_next_location_product_page(page)
        {
            let getLocationModalUrl = $('.locationForm').data('location-modal-route');

            $.ajax({
                url: getLocationModalUrl+"?page="+page,
                success:function(data)
                {
                    $('#locationEditModal > div').html(data);
                    $(".inputs-container").scrollTop($('.inputs-container')[0].scrollHeight + $('.inputs-container')[0].clientHeight);
                }
            });
        }
    });
</script>
