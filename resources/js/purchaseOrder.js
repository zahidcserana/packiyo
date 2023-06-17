window.PurchaseOrder = function (keyword='') {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)
    $(document).find('select:not(.custom-select)').select2();

    const tableSelector = 'purchase-orders';
    $(document).ready(function () {

        if(keyword!=''){
            $('.searchText').val(keyword);
            window.dtInstances['#'+tableSelector+'-table'].search(keyword).draw();
        }
    });

    window.datatables.push({
        selector: '#'+tableSelector+'-table',
        resource: 'purchase_orders',
        ajax: {
            url: '/purchase_orders/data-table',
            data: function (data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['purchase_orders'] = data
            }
        },
        order: [3, 'desc'],
        columns: [
            {
                "orderable": false,
                "title": `<div class="custom-datatable-checkbox-container-header">
                            <div>
                                <input id="select-all-checkboxes" type="checkbox" value="0">
                                <label for="select-all-checkboxes"></label>
                            </div>
                          </div>`,
                "class": "text-left",
                "createdCell": (cell) => {
                    $(cell).addClass("d-flex")
                },
                "data": function (data) {
                    let closeButton = data.is_3pl
                        ? `
                            <button type="button" class="d-flex table-icon-button" title="Close purchase order" data-url="purchase_orders/close/${ data.id }" data-toggle="modal" data-target="#closePurchaseOrderModal">
                                <i class="picon-event-scheduled-light icon-lg"></i>
                            </button>
                        ` : ``

                    return `
                        <div class="d-flex">
                            <div class="custom-datatable-checkbox-container">
                                <div>
                                    <input name="bulk-edit[${data.id}]" id="bulk-edit-${data.id}" class="custom-datatable-checkbox" type="checkbox" value="0">
                                    <label class="mb-0" for="bulk-edit-${data.id}"></label>
                                </div>
                            </div>
                            <button type="button" class="table-icon-button" data-id="${data.id}">
                                <a href="/purchase_orders/${data.id}/edit">
                                    <i class="picon-edit-filled icon-lg" title="Edit"></i>
                                </a>
                            </button>
                            ${closeButton}
                        </div>
                    `
                },
            },
            {
                "title": "PO number",
                "data": "number",
                "name": "number",
                "width": "30%"
            },
            {
                "title": "Status",
                "data": "status",
                "name": "purchase_order_statuses.name"
            },
            {
                "title": "Ordered at",
                "name": "ordered_at",
                "data": "ordered_at"
            },
            {
                "title": "Expected date",
                "name": "expected_at",
                "data": "expected_at"
            },
            {
                "title": "Vendor",
                "name": "supplier_contact_information.name",
                "data": function (data) {
                    return data.supplier !== '' ? data.supplier['name'] : ''
                }
            },
            {
                "title": "Warehouse",
                "name": "warehouse_contact_information.name",
                "data": function (data) {
                    return data.warehouse !== '' ? data.warehouse['name'] : ''
                }
            },
            {
                "non_hiddable": true,
                "searchable": false,
                "orderable": false,
                "class": "text-right",
                "data": function (data) {
                    if (! data.is_3pl) {
                        return
                    }

                    return `
                        <a
                            type="button"
                            class="btn bg-logoOrange text-white px-5 font-weight-700"
                            href="${data.link_receive['url']}"
                            title="Receive purchase order"
                        >
                            Receive
                        </a>
                    `
                }
            }
        ],
        dropdownAutoWidth : true,
    })

    $(document).ready(function() {
        checkDeleteButton();
        dateTimePicker();
        deleteItemFromTableButton();

        let customerSelect = $('.customer_id');
        let warehouseSelect = $('.enabled-for-customer[name="warehouse_id"]');
        let orderStatusSelect = $('.enabled-for-customer[name="purchase_order_status_id"]');
        let supplierSelect = $('#purchase-order-create-form .enabled-for-customer[name="supplier_id"]');
        let selectedSupplier = 0;
        let productIds = [];
        const purchaseOrderItemCreate = $("#createOrderItemsSelection")
        const createSearchSelect = $('#createOrderItemsSelection .product_id');

        function changeSelectInputUrlAjax(selectInputToChange, value) {
            if (selectInputToChange && selectInputToChange.data('ajax--url')) {
                selectInputToChange.select2('destroy');
                selectInputToChange.data('ajax--url', selectInputToChange.data('ajax--url').replace(/\/\w+?$/, '/' + value));
                selectInputToChange.select2();

                if ($('#purchase-order-create-form').is(':visible')) {
                    selectInputToChange.select2({
                        dropdownParent: $("#purchase-order-create-form")
                    });
                }
            }
        }

        customerSelect.on('select2:select', function () {
            warehouseSelect.empty();
            supplierSelect.empty();
        });

        customerSelect.on('change', function (event) {
            let customerId = customerSelect.val();
            let selectedStatus = orderStatusSelect.val();

            orderStatusSelect.empty();

            if (customerId) {
                changeSelectInputUrlAjax(warehouseSelect, customerId);
                changeSelectInputUrlAjax(supplierSelect, customerId);

                customerSelect.trigger('ajaxSelectOldValueUrl:toggle');

                $.get("/purchase_orders/get_order_status/" + customerId, function(data){
                    let results = data.results;
                    $.map( results, function(result) {
                        let selected = Number(result.id) === Number(selectedStatus);
                        orderStatusSelect.append(new Option(result.text, result.id, selected, selected));
                    })
                });
            }
        }).trigger('change');

        $('#add_item').click(function (event) {
            let lastOrderItemFields = $('.order-item-fields:not(.order-item-deleted):last');

            lastOrderItemFields.find('select').select2('destroy');

            let orderItemFieldsHtml = lastOrderItemFields[0].outerHTML;
            let index = orderItemFieldsHtml.match(/\[(\d+?)\]/);
            let orderItemFields = $(orderItemFieldsHtml.replace(/\[\d+?\]/g, '[' + (parseInt(index[1]) + 1) + ']'));

            $('#item_container').append(orderItemFields);
            $('.order-item-fields:last').find('input[type=hidden]').remove();
            $('.order-item-fields:last').show();

            lastOrderItemFields.find('select').select2();
            orderItemFields.find('select').select2();
            $(orderItemFields).find('input.received-quantity').val(0);
            $(orderItemFields).find('input.quantity').val(0);

            checkDeleteButton();
            event.preventDefault();
        });

        function appendProductRow(data){

            let index = 0;
            let rows = $(document).find('#purchase-order-create-form .productRow');
            if (rows.length) {
                let lastIndex = parseInt(rows.last().data('index'));
                index = ++ lastIndex;
            }

            if ($.inArray(data.id, productIds) !== -1) {
                let existedProdQuantity = parseInt($("input[data-product='" + data.id +"']").val());
                $("input[data-product='" + data.id +"']").val(existedProdQuantity + data.quantity);
            } else {
                productIds.push(data.id);
                let productRow =
                    `<tr class="productRow" data-index="` + index + `">` +
                    `<td>` + data.text + `<input type="hidden" name="purchase_order_items[` + index + `][product_id]" value="` + data.id + `"></td>` +
                    `<td>` + data.barcode + `</td>` +
                    `<td>` +
                    `<div class="form-group mb-0 mx-2 text-left d-flex flex-column justify-content-end mb-3 ">` +
                    `<div class="input-group input-group-alternative input-group-merge">` +
                    `<input type="number" data-product="` + data.id + `" class="reset_on_delete quantity form-control font-weight-600 text-black h-auto ordered-quantity" name="purchase_order_items[` + index + `][quantity]" value="`+data.quantity+`" min="0" />` +
                    `</div>` +
                    `</div>` +
                    `</td>` +
                    `<td>` +
                    `<div class="form-group mb-0 mx-2 text-left d-flex flex-column justify-content-end mb-3">` +
                    `<div class="input-group input-group-alternative input-group-merge">` +
                    `<input type="number" class="reset_on_delete quantity form-control font-weight-600 text-black h-auto sell-ahead-quantity " name="purchase_order_items[` + index + `][quantity_sell_ahead]" value="0" min="0" />` +
                    `</div>` +
                    `</div>` +
                    `</td>`;

                if (!window.location.href.endsWith('/create')) {
                    productRow += `<td>` +
                        `<div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input d-flex justify-content-center">` +
                        `<input type="number" class="quantity-input form-control font-weight-600 px-2 py-1" readonly name="purchase_order_items[` + index + `][quantity_to_receive]" value="0" />` +
                        `</div>` +
                        `</td>`;
                }

                productRow += '</tr>';

                $('#purchase-order-create-form .searchedProducts #item_container').append(productRow);
                $('#purchase-order-create-form .searchedProducts #item_container').find('.ordered-quantity:last').change();
            }
        }

        createSearchSelect.select2({
            dropdownParent: purchaseOrderItemCreate,
            ajax: {
                processResults: function (data, params) {
                    return  {
                        results: data.results,
                    }
                },
                data: function (params) {
                    return  {
                        term: params.term,
                        supplier: supplierSelect.val()
                    }
                },
            },
        });

        createSearchSelect.on('select2:select', function (e) {
            let data = e.params.data;
            appendProductRow(data);

            createSearchSelect.val(null).trigger('change');
        });

        $('#quantityRejectedModal').on('show.bs.modal', function (e) {
            $('#quantityRejectedModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/purchase_orders/getRejectedPurchaseOrderItemModal/' + itemId,

                success:function(data) {
                    $('#quantityRejectedModal>div').html(data);
                },
            });
        })

        $(document).on('click', '.globalSave', function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()
            let _form = $(this).closest('#globalForm');

            let formData = new FormData();

            const forms = _form.find('form');

            $.each(forms, function (index, form) {
                let data = $(form).serializeArray()
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                })

                if ($(form).find('[name="tags[]"]').length && !formData.has('tags[]')) {
                    formData.append('tags[]', '')
                }
            })

            $.ajax({
                type: 'POST',
                url: forms.attr('action'),
                enctype: 'multipart/form-data',
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (forms.data('success-redirect')) {
                        window.location.href = forms.data('success-redirect')
                    } else {
                        $('.smallForm').removeClass('editable');
                        toastr.success(data.message)
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                },
                error: function (messages) {
                    if (messages.responseJSON.errors) {
                        $.each(messages.responseJSON.errors, function (key, value) {
                            toastr.error(value)
                        });
                    }
                    $(document).find('.validate-error').eq(0).closest('form').addClass('editable')
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            })
        })

        $('.deletePurchaseOrder').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('#deletePurchaseOrderForm');
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
                    $('#purchaseOrderDeleteModal').modal('toggle');
                    $('#purchaseOrderEditModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#purchase-orders-table'].ajax.reload()
                }
            });
        });

        $('#barcode').change(function(event){
            let barcode = $(this).val();
            if (barcode!=''){

                $("[barcode="+barcode+"]").each(function(i,el){
                    let oldValue = parseInt($(el).val());
                    $(el).val(oldValue+1);
                });

                $(this).val('');
            }
        });

        supplierSelect.on('change', function(e){

            selectedSupplier = this.value;
            $('.additional-action').each(function() {
                $(this).prop('disabled', selectedSupplier <= 0);
            });
        });

        $('.additional-action').on('click', function (e) {

            $.ajax({
                type:'GET',
                serverSide: true,
                data:{
                    action: $(this).data("action"),
                    supplier: selectedSupplier
                },
                url:'/purchase_orders/filterProducts/',

                success:function(response) {

                    $.each(response.results, function (index, data) {

                        appendProductRow(data);
                    });
                },
            });
        });

        //Import and export modals
        $('.import-purchase-orders').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.import-purchase-orders-form');
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
                    $('#csv-filename').empty();

                    toastr.success(data.message);

                    window.dtInstances['#purchase-orders-table'].ajax.reload()
                },
                error: function (response) {
                    if (response.status != 504) {
                        $('#csv-filename').empty();

                        toastr.error('Invalid CSV data');

                        if (typeof response.responseJSON !== 'undefined') {
                            appendValidationMessages($('#import-purchase-orders-modal'), response);
                        }
                    }
                }
            });

            $('.import-purchase-orders-form')[0].reset()
            $('#import-purchase-orders-modal').modal('hide');
            toastr.info('Purchase order import started. You may continue using the system');
        });

        $('.export-purchase-orders').click(function () {
            $('#export-purchase-orders-modal').modal('toggle');
        });

        $('#purchase-orders-csv-button').on('change', function (e) {
            if (e.target.files) {
                if (e.target.files[0]) {
                    let filename = e.target.files[0].name
                    $('#csv-filename').append(
                        '<h5 class="heading-small">' +
                        'Filename: ' + filename +
                        '</h5>'
                    )
                }

                $('#import-purchase-orders-modal').focus()
            }
        })

        if( keyword !='' ){
            table.search(keyword).draw();
        }

        $(document).on('click', '.productForm .saveButton:not(#submit-button)', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()
            let _form = $(this).closest('#globalForm');

            let formData = new FormData();

            const forms = _form.find('form');

            $.each(forms, function (index, form) {
                let data = $(form).serializeArray()
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                })
            })

            $.ajax({
                type: 'POST',
                url: forms.attr('action'),
                enctype: 'multipart/form-data',
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('.smallForm').removeClass('editable');
                    toastr.success(data.message)
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                },
                error: function (messages) {
                    if (messages.responseJSON.errors) {
                        $.each(messages.responseJSON.errors, function (key, value) {
                            toastr.error(value)
                        });
                    }
                    $(document).find('.validate-error').eq(0).closest('form').addClass('editable')
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            })
        });

        $('#bulk-edit-modal').on('show.bs.modal', function () {
            let ids = []
            let form = $('#bulk-edit-form')

            $('input[name^="bulk-edit"]').each(function() {
                if($(this).prop('checked')) {
                    let purchaseOrderId = $(this).attr('name')
                    purchaseOrderId = purchaseOrderId.replace(/[^0-9]/g,'')

                    ids.push(parseInt(purchaseOrderId))
                }
            })

            $('#number-of-selected-items').text(ids.length)
            $('#item-type').text('Purchase Orders')
            $('#model-ids').val(ids)

            form.attr('action', '/purchase_orders/bulk-edit')
            form.serialize()
        })

        $(document).on('change', '.ordered-quantity', function() {
            let orderedQuantity = $(this).val()
            let sellAheadInput = $(this).closest('.productRow').find('.sell-ahead-quantity');

            sellAheadInput.attr('max', orderedQuantity);

            if (sellAheadInput.val() > orderedQuantity) {
                sellAheadInput.val(orderedQuantity);
            }
        });

        $(document).on('change', '.sell-ahead-quantity', function() {
            let sellAheadInput = $(this)
            let orderedQuantity = $(this).closest('.productRow').find('.ordered-quantity').val();

            if (sellAheadInput.val() > orderedQuantity) {
                sellAheadInput.val(orderedQuantity);
            }
        });
    })
};
