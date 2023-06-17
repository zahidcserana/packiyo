<form method="post" action="{{ route('purchase_orders.update', ['purchase_order' => $purchaseOrder]) }}" autocomplete="off" data-type="POST" enctype="multipart/form-data"
      class="modal-content purchaseOrderForm supplier_container bg-white">
    @csrf
    @method('PUT')
    <div class="modal-header px-0">
        <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
            <h6 class="modal-title text-black text-left"
                id="modal-title-notification">{{ __('Edit purchase order') }}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="text-black">&times;</span>
            </button>
        </div>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        <div class="">
            <div class="w-100 d-lg-flex">
                <div class="w-50">
                    <div class="searchSelect">
                        @include('shared.forms.new.ajaxSelect', [
                        'url' => route('purchase_order.filterWarehouses', ['customer' => $purchaseOrder->customer->id]),
                        'name' => 'warehouse_id',
                        'className' => 'ajax-user-input warehouse_id enabled-for-customer',
                        'placeholder' => __('Enter Warehouse'),
                        'label' => __('Warehouse'),
                        'default' => [
                            'id' => $purchaseOrder->warehouse->id ?? old('warehouse_id'),
                            'text' => $purchaseOrder->warehouse->contactInformation->name ?? ''
                        ],
                        'fixRouteAfter' => '.ajax-user-input.customer_id'
                    ])
                    </div>
                </div>
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="tracking_number"
                               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking number') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Tracking number') }}"
                                type="text"
                                name="tracking_number"
                                value="{{ $purchaseOrder->tracking_number ?? '' }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 d-lg-flex">
                <div class="w-50">
                    <div class="searchSelect">
                        @include('shared.forms.new.ajaxSelect', [
                        'url' => route('purchase_order.filterSuppliers', [$purchaseOrder->customer->id]),
                        'name' => 'supplier_id',
                        'readonly'=> true,
                        'className' => 'ajax-user-input supplier_id enabled-for-customer',
                        'placeholder' => __('Enter Vendor'),
                        'label' => __('Vendor'),
                        'default' => [
                            'id' => $purchaseOrder->supplier->id ?? old('supplier_id'),
                            'text' => $purchaseOrder->supplier->contactInformation->name ?? ''
                        ],
                        'fixRouteAfter' => '.ajax-user-input.customer_id'
                    ])
                    </div>
                </div>
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="tracking_url"
                               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking URL') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Tracking URL') }}"
                                type="text"
                                name="tracking_url"
                                value="{{ $purchaseOrder->tracking_url ?? '' }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 d-lg-flex">
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="number"
                               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('PO Number') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('PO Number') }}"
                                type="text"
                                name="number"
                                value="{{ $purchaseOrder->number ?? '' }}"
                            >
                        </div>
                    </div>
                    <input type="hidden" name="customer_id" value="{{ $purchaseOrder->customer->id ?? $sessionCustomer->id}}" class="customer_id" />
                </div>
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="ordered_at"
                               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Ordered at') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2  datetimepicker"
                                placeholder="{{ __('Ordered at') }}"
                                type="text"
                                name="ordered_at"
                                value="{{ $purchaseOrder->ordered_at ?? '' }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 d-lg-flex">
                <div class="w-50">
                    @include('shared.forms.editSelectTag', [
                        'containerClass' => 'form-group mb-0 mx-2 text-left mb-3',
                        'labelClass' => '',
                        'selectClass' => 'select-ajax-tags',
                        'label' => __('Tags'),
                        'minimumInputLength' => 3,
                        'default' => $purchaseOrder->tags
                    ])
                </div>
                <div class="w-50">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               data-id="expected_at"
                               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Expected at') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2 datetimepicker"
                                placeholder="{{ __('Expected at') }}"
                                type="text"
                                name="expected_at"
                                value="{{ $purchaseOrder->expected_at ?? '' }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="searchSelect" id="orderItemsSelection">
            <label data-id="purchase_order_items"></label>
            @include('shared.forms.ajaxSelect', [
                'url' => route('purchase_order.filterProducts', ['purchaseOrder' => $purchaseOrder]),
                'name' => 'purchase_order_items[0][product_id]',
                'className' => 'ajax-user-input product_id',
                'placeholder' => __('Search products'),
                'label' => '',
                'labelClass' => 'd-block',
                'fixRouteAfter' => '.ajax-user-input.customer_id'
            ])
        </div>
        <div class="table-responsive table-overflow items-table searchedProducts">
            <table class="col-12 table align-items-center table-flush">
                <thead>
                <tr>
                    <th scope="col">{{ __('Product') }}</th>
                    <th scope="col">{{ __('Barcode') }}</th>
                    <th scope="col">{{ __('Quantity') }}</th>
                    <th scope="col">{{ __('Sell Ahead Quantity') }}</th>
                    <th scope="col">{{ __('Quantity Received') }}</th>
                </tr>
                </thead>
                <tbody id="item_container">
                    @foreach($purchaseOrder->purchaseOrderItems as $key => $orderItem)
                        <input type="hidden" name="purchase_order_items[{{ $key }}][purchase_order_item_id]" value="{{ $orderItem->id }}">
                        <tr class="productRow" data-index="{{ $key }}">
                            <td>
                                {!! nl2br('SKU: ' . $orderItem->product->sku . ',') !!}
                                {!! 'NAME: ' . $orderItem->product->name !!}
                                <input type="hidden" name="purchase_order_items[{{ $key }}][product_id]" value="{{ $orderItem->product->id }}">
                            </td>
                            <td>
                                {!! $orderItem->product->barcode !!}
                            </td>
                            <td>
                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">
                                    <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity]" value="{{ $orderItem->quantity }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">
                                    <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[{{ $key }}][quantity_sell_ahead]" value="{{ $orderItem['quantity_sell_ahead'] }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">
                                    <input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[{{ $key }}][quantity_received]" value="{{ $orderItem->quantity_received }}"/>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button"
                data-toggle="modal" data-target="#purchaseOrderDeleteModal" data-token="{{ csrf_token() }}" data-url="{{ route('purchase_orders.destroy', ['id' => $purchaseOrder->id, 'purchase_order' => $purchaseOrder]) }}"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700">
            {{ __('Delete') }}
        </button>
        <button type="submit"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-submit-button"
                id="submit-button">
            {{ __('Save') }}
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        const modalEdit = $('#purchaseOrderEditModal')

        $('.warehouse_id').select2({
            dropdownParent: modalEdit
        });

        $('.supplier_id').select2({
            dropdownParent: modalEdit
        });

        $('.modal-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.purchaseOrderForm');
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
                    modalEdit.modal('toggle');

                    toastr.success(data.message)

                    window.location.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        modalEdit,
                        response
                    )
                }
            });
        });

        const searchSelect = $("#orderItemsSelection .product_id")

        searchSelect.select2({
            dropdownParent: $("#orderItemsSelection"),
            ajax: {
                processResults: function (data) {
                    return  {
                        results: data.results,
                    }
                },
                data: function (params) {
                    return  {
                        term: params.term,
                        supplier: {{ $purchaseOrder->supplier->id }}
                    }
                },
            },
        })

        searchSelect.on('select2:select', function (e) {
            let data = e.params.data;
            let index = 0;
            let rows = $(document).find('#purchaseOrderEditModal .productRow');
            if (rows.length) {
                let lastIndex = parseInt(rows.last().data('index'))
                index = ++lastIndex
            }
            let productRow =
                `<tr class="productRow" data-index="` + index + `">` +
                `<td>` + data.text + `<input type="hidden" name="purchase_order_items[` + index + `][product_id]" value="` + data.id + `"></td>` +
                `<td>` + data.barcode + `</td>` +
                `<td>` +
                `<div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">` +
                `<input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[` + index + `][quantity]" value="0" min="0" />` +
                `</div>` +
                `</td>` +
                `<td>` +
                `<div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">` +
                `<input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" name="purchase_order_items[` + index + `][quantity_sell_ahead]" value="0" min="0" />` +
                `</div>` +
                `</td>` +
                `<td>` +
                `<div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">` +
                `<input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[` + index + `][quantity_received]" value="0" />` +
                `</div>` +
                `</td>` +
                `</tr>`

            $('#purchaseOrderEditModal .searchedProducts #item_container').append(productRow)

            searchSelect.val(null).trigger('change');
        });

        $('#purchaseOrderDeleteModal').on('show.bs.modal', function (e) {
            let deleteURL = $(e.relatedTarget).data('url')
            let token = $(e.relatedTarget).data('token')

            $('#deletePurchaseOrderForm').attr('action', deleteURL)
            $('#formToken').attr('value', token)
        })

        modalEdit.find('.warehouse_id').select2({
            dropdownParent: modalEdit
        });
    });
</script>
