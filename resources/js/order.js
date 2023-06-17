window.Order = function (keyword='') {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm);
    const tableSelector = 'orders';

    $(document).ready(function () {
        if (keyword != '') {
            $('.searchText').val(keyword);
            window.dtInstances['#'+tableSelector+'-table'].search(keyword).draw();
        }
    });

    window.datatables.push({
        selector: '#'+tableSelector+'-table',
        resource: 'orders',
        ajax: {
            url: '/order/data-table',
            data: function (data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request
                data.from_date = $('#dateFilter').val()

                window.queryUrl(request)

                window.exportFilters[tableSelector] = data
            }
        },
        aaSorting: [],
        order: [4, 'desc'],
        columns: [
            {
                orderable: false,
                "class": "text-left",
                "createdCell": (cell) => {
                    $(cell).addClass("d-flex")
                },
                "title": `<div class="custom-datatable-checkbox-container-header">
                            <div>
                                <input id="select-all-checkboxes" type="checkbox" value="0">
                                <label for="select-all-checkboxes"></label>
                            </div>
                          </div>`,
                "name": "orders.id",
                "data": function (data) {
                    let bulkEditCheckbox = `<div class="custom-datatable-checkbox-container">
                            <div>
                                <input name="bulk-edit[${data.id}]" id="bulk-edit-${data.id}" class="custom-datatable-checkbox" type="checkbox" value="0" data-id="${data.id}">
                                <label class="mb-0" for="bulk-edit-${data.id}"></label>
                            </div>
                        </div>`;

                    let viewButton = `<button type="button" class="table-icon-button" data-id="${data.id}" data-toggle="modal" data-target="#orderViewModal">
                            <i class="picon-show-light icon-lg" title="View"></i>
                        </button>`;
                    let orderSlipButton = `<a target="_blank" title="Order Slip" href="${data.order_slip_url}">
                            <i class="picon-receipt-light icon-lg" title="Order Slip"></i>
                        </a>`;

                    return bulkEditCheckbox + viewButton + orderSlipButton;
                },
            },
            {
                "title": "Number",
                "data": function (data) {
                    return `
                        <a href="${data.link_edit}">${data.number}</a>
                    `
                },
                "name": "number",
                "visible": false
            },
            {
                "title": "Customer",
                "data": function (data) {
                    return data.customer['name']
                },
                "name": "customer_contact_information.name",
                "visible": false,
                orderable: false,
            },
            {
                "title": "Status",
                "data": "order_status_name",
                "name": "order_status_id",
                "class" : "orderStatus",
                render : function(data, type, row) {
                    return '<div class="status">' +
                        '<span class="statusBg p-1 border-5" style="background-color: ' + row.order_status_color + '">' +
                        '<span style="opacity: 0">' + data + '</span>' +
                        '</span>' +
                        '<span class="statusText" style="color: ' + row.order_status_color + ' ">' + data + '</span>' +
                        '</div>'
                },
                "visible": false,
                orderable: false,
            },
            {
                "title": "Date",
                "data": "ordered_at",
                "name": "ordered_at",
                "visible": false
            },
            {"title": "Name", "data": "shipping_name", "name": "contact_informations.name", "visible": false, orderable: false},
            {"title": "Address", "data": "shipping_address", "name": "contact_informations.address", "visible": false, orderable: false},
            {"title": "City", "data": "shipping_city", "name": "contact_informations.city", "visible": false, orderable: false},
            {"title": "State", "data": "shipping_state", "name": "contact_informations.state", "visible": false, orderable: false},
            {"title": "Zip", "data": "shipping_zip", "name": "contact_informations.zip", "visible": false, orderable: false},
            {"title": "Country", "data": "shipping_country", "name": "contact_informations.country.name", "visible": false, orderable: false},
            {"title": "Email", "data": "shipping_email", "name": "contact_informations.email", "visible": false, orderable: false},
            {"title": "Phone", "data": "shipping_phone", "name": "contact_informations.phone", "visible": false, orderable: false},
            {"title": "Ready to ship", "data": "ready_to_ship", "name": "ready_to_ship", "visible": false},
            {"title": "Ready to pick", "data": "ready_to_pick", "name": "ready_to_pick", "visible": false},
            {"title": "Allow partial", "data": "allow_partial", "name": "allow_partial", "visible": false},
            {
                "title": "In Tote",
                "data": function (data) {
                    if (data.tote != null) {
                        return `
                            <a href="${data.tote.url}">${data.tote.name}</a>
                        `
                    }
                    return null
                },
                "name": "totes.name",
                "visible": false,
                orderable: false,
            },
            {"title": "Priority", "data": "priority", "name": "priority","visible": false},
            {"title": "Score", "data": "priority_score", "name": "priority_score", "visible": false},
            {"title": "Order channel", "data": "order_channel_name", "name": "order_channel", "visible": false, orderable:false},
            {
                'title': 'Tags',
                'data': 'tags',
                'name': 'tags.name',
                'visible': false,
                orderable: false,
            },
            {
                'title': 'Hold until',
                'data': 'required_shipping_date_at',
                'name': 'required_shipping_date_at',
                'visible': false
            },
            {
                'title': 'Required ship date',
                'data': 'shipping_date_before_at',
                'name': 'shipping_date_before_at',
                'visible': false
            },
        ]
    });

    $(document).ready(function() {
        checkDeleteButton();
        deleteItemFromTableButton();
        dateTimePicker();
        dtDateRangePicker();
        $(document).find('select:not(.custom-select)').select2();

        $('#order-return-modal').on('show.bs.modal', function (e) {
            $('#order-return-modal .modal-body').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let orderId = $('#order-return-modal').data('id');
            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/order/getOrderReturnForm/' + orderId,

                success:function(data) {
                    $('#order-return-modal .modal-body').html('');
                    $('#order-return-modal .modal-body').html(data);
                },
            });
        });

        let orderStatusSelect = $('.enabled-for-customer[name="order_status_id"]');
        let customerSelect = $('.customer_id');
        let enabledForCustomer = $('.enabled-for-customer');
        let shippingMethodSelect = $('.shipping_method_id');
        let orderReturnShippingMethodSelect = $('.shipping-method-order-return');
        let productSelect = $('.order-item-input');
        let shippingMethodSelectUrl
        let orderReturnShippingMethodSelectUrl
        let productFilterUrl

        if (typeof shippingMethodSelectUrl === 'undefined') {
            shippingMethodSelectUrl = shippingMethodSelect.data('ajax--url');
        }

        if (typeof orderReturnShippingMethodSelectUrl === 'undefined') {
            orderReturnShippingMethodSelectUrl = orderReturnShippingMethodSelect.data('ajax--url');
        }

        const searchSelect = $('.searchSelect .order-item-input');

        searchSelect.select2({
            ajax: {
                processResults: function (data, params) {
                    return  {
                        results: data.results,
                    }
                },
                data: function (params) {
                    return  {
                        term: params.term,
                    }
                },
            },
        })

        if(window.location.pathname === '/order/create') {
            searchSelect.prop('disabled', true);
        }

        if (typeof productFilterUrl === 'undefined') {
            productFilterUrl = productSelect.data('ajax--url');
            if(typeof productFilterUrl !== 'undefined') {
                const result = productFilterUrl.substring(
                    productFilterUrl.lastIndexOf("/") + 1
                );
                if (isNumberic(result)) {
                    productFilterUrl = productFilterUrl.substr(
                        0,
                        productFilterUrl.lastIndexOf("/")
                    );
                }
            }
        }

        function toggleInputs(){
            if (customerSelect.val() === '' || customerSelect.val() ===  null) {
                orderStatusSelect.prop('disabled', true);

                orderStatusSelect.append(new Option('Select', 'title', true, false));

                if (orderStatusSelect.length > 0) {
                    orderStatusSelect[0].options[0].disabled = true;
                }
            } else {
                enabledForCustomer.prop('disabled', false);
            }
        }
        toggleInputs();

        customerSelect.on('change', function () {
            let customerId = customerSelect.val();
            let selectedStatus = orderStatusSelect.val();

            orderStatusSelect.empty();
            shippingMethodSelect.empty();
            productSelect.empty();

            searchSelect.prop('disabled', false);

            toggleInputs();

            if (customerId) {
                if (shippingMethodSelect && shippingMethodSelect.data('ajax--url')) {
                    shippingMethodSelect.select2('destroy');
                    shippingMethodSelect.data('ajax--url', shippingMethodSelectUrl + '/' + customerId);
                    shippingMethodSelect.select2();
                }

                if (orderReturnShippingMethodSelect && orderReturnShippingMethodSelect.data('ajax--url')) {
                    orderReturnShippingMethodSelect.select2('destroy');
                    orderReturnShippingMethodSelect.data('ajax--url', orderReturnShippingMethodSelectUrl + '/' + customerId);
                    orderReturnShippingMethodSelect.select2();
                }

                if (productSelect && productSelect.data('ajax--url')) {
                    productSelect.select2('destroy');
                    productSelect.data('ajax--url', productFilterUrl + '/' + customerId);
                    productSelect.select2();
                }

                $.get("/orders/get_order_status/" + customerId, function(data){
                    let results = data.results;

                    orderStatusSelect.append(new Option('Pending', 'pending', false, false))

                    $.map(results, function(result) {
                        if ($(orderStatusSelect).find("option[value='" + result.id + "']").length === 0){
                            let selected = Number(result.id) === Number(selectedStatus);
                            orderStatusSelect.append(new Option(result.text, result.id, selected, selected));
                        }
                    })
                });

                if (this.value !== this.defaultValue) {
                    $('#item_container').empty();
                }
            }
        }).trigger('change');

        if($('#fill-information').val()) {
            $('.billing_contact_information').hide();
        }

        $('#fill-information').click(function () {
            if ($(this)[0].checked) {
                $('.billing_contact_information').show();
                $('.sizing').addClass('col-xl-4').removeClass('col-xl-6');

            }else{
                $('.billing_contact_information').hide();
                $('.sizing').addClass('col-xl-6').removeClass('col-xl-4');
            }
        });

        $('#orderViewModal').on('show.bs.modal', function (e) {
            $('#orderViewModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/order/getOrder/' + itemId,

                success:function(data) {
                    $('#orderViewModal>div').html(data);
                },
            });
        })

        searchSelect.on('select2:select', function (e) {
            let data = e.params.data;
            let dataItems = data.text.split(',')
            let index = 0;
            let rows = $('#item_container .productRow');
            if (rows.length) {
                let lastIndex = parseInt(rows.last().data('index'))
                index = ++ lastIndex
            }
            let image = '/img/no-image.png'
            if(data.image !== null) {
                image = data.image.source
            }
            let deleteButton, status;
            if(data.kit_type == 1) {
                deleteButton = '';
            } else {
                deleteButton =
                    `<input type="hidden" name="order_items[${index}][product_id]" value="${data.id}"/>
                    <i class="picon-trash-filled" title="Delete"></i>`;
            }
            let productRow = `
                <tr class="productRow parentProductRow" data-index="${index}">
                    <td>
                        <img src="${image}" alt="">
                        <input type="hidden" name="order_items[${index}][img]" value="${image}">
                    </td>
                    <td>
                        <input type="hidden" name="order_items[${index}][is_kit_item]" value="false">
                        SKU: ${data.sku} </br>
                        Name: <a href="/product/${data.id}/edit" target="_blank">${data.name}</a> </br>
                        <input type="hidden" name="order_items[${index}][text]" value="${data.text}">
                        <input type="hidden" name="order_items[${index}][name]" value="${data.name}">
                        <input type="hidden" name="order_items[${index}][id]" value="${data.id}">
                        <input type="hidden" name="order_items[${index}][sku]" value="${data.sku}">
                    </td>
                    <td>
                        <span class="price-value">
                            ${data.price}
                        </span>
                        <input type="hidden" name="order_items[${index}][price]" value="${data.price}">
                    </td>
                    <td>
                        <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                            <input
                                type="number"
                                data-index="${index}"
                                data-child-count="${data.child_products.length}"
                                class="quantity-input form-control font-weight-600 px-2 py-1"
                                name="order_items[${index}][quantity]"
                                value="1"
                            />
                        </div>
                    </td>
                    <td class="quantity-value">
                        <span class="price-value">0</span>
                    </td>
                    <td class="quantity-value">
                        <span class="price-value">0</span>
                    </td>
                    <td class="quantity-value">
                        <span class="price-value">0</span>
                    </td>
                    <td class="quantity-value">
                        <span class="price-value">0</span>
                    </td>
                    <td class="quantity-value">
                        <span class="price-value">0</span>
                    </td>
                    <td class="item-total-price">
                        ${parseInt(data.quantity) * parseFloat(data.price)}
                    </td>
                    <td class="${data.kit_type == 1 ? '' : 'delete-row productList' }">
                        <input type="hidden" name="order_items[${index}][product_id]" value="${data.id}"/>
                        ${deleteButton}
                    </td>
                </tr>`

            $('.searchedProducts #item_container').append(productRow)


            let child_products = data.child_products;
            child_products.forEach(function(child_product) {
                let index = 0;
                let rows = $('#item_container .productRow');
                if (rows.length) {
                    let lastIndex = parseInt(rows.last().data('index'))
                    index = ++ lastIndex
                }
                let src = child_product.product_images.length ? child_product.product_images[0].source : data.default_image;
                let childProductPrice = child_product.price ?? 0;
                let childProductRow = `
                    <tr class="productRow" data-index="${index}">
                        <input type="hidden" name="order_items[${index}][parent_product_id]" value="${data.id}"/>
                        <td class="pl-2">
                            <img src="${src}" alt="">
                            <input type="hidden" name="order_items[${index}][img]" value="${image}">
                        </td>

                        <td class="product-text">
                            <input type="hidden" name="order_items[${index}][is_kit_item]" value="true">
                            SKU:${child_product.sku} </br>
                            Name: <a href="/product/${child_product['id']}/edit" target="_blank">${child_product.name}</a> </br>
                            <input type="hidden" name="order_items[${index}][text]" value="SKU:${child_product.sku}</br> NAME:${child_product.name}">
                            <input type="hidden" class="order-item-${child_product.id}" value="0" name="order_items[${index}][cancelled]">
                            <input type="hidden" name="order_items[${index}][name]" value="${child_product.name}">
                            <input type="hidden" name="order_items[${index}][id]" value="${child_product.id}">
                            <input type="hidden" name="order_items[${index}][sku]" value="${child_product.sku}">
                        </td>
                        <td>
                            <span class="price-value">
                                0
                            </span>
                            <input type="hidden" name="order_items[${index}][price]" value="${childProductPrice}">
                        </td>
                        <td class="child_product_quantity">
                            <div class="input-group input-group-alternative input-group-merge font-sm tableSearch number-input">
                            <input type="hidden" name="order_items[${index}][child_quantity]" value="${child_product.quantity}">
                            <input
                                data-quantity="${child_product.quantity}"
                                readonly
                                type="number"
                                class="quantity-input form-control font-weight-600 px-2 py-1 childquantity-input_${index}"
                                name="order_items[${index}][quantity]"
                                value="${ data.quantity > 0 ? parseInt(child_product.quantity) : 0 }"
                            />
                            </div>
                        </td>
                        <td>
                            <span class="price-value">0</span>
                        </td>
                        <td>
                            <span class="price-value">0</span>
                        </td>
                        <td>
                            <span class="price-value">0</span>
                        </td>
                        <td>
                            <span class="price-value">0</span>
                        </td>
                        <td class="">

                        </td>
                        <td class="">
                            <input type="hidden" name="order_items[${index}][product_id]" value="${child_product.id}"/>
                            <button type="button" class="text-white mx-auto px-4 py-2 mr-1 border-0 cancelOrderKit cancelOrderKit${child_product.id}"
                                    data-id="${child_product.id}" data-toggle="modal" data-target="#cancelKitItem">
                                Cancel
                            </button>
                        </td>
                    </tr>
                `
                if (data.kit_type == 1) { //static
                    $('.searchedProducts #item_container').append(childProductRow)
                    $('.productList .cancelOrderItem'+index).hide()
                }
            });

            searchSelect.val(null).trigger('change');

            $('#item_container .quantity-input').not('[readonly]').last().change();

            setOrderDetails()
        });

        $('#cancelKitItem').on('show.bs.modal', function (e) {
            let itemId = $(e.relatedTarget).data('id');

            $('#cancelKitItem .cancelled-order-item').attr('data-product-id', itemId);
        })

        $(document).on('click', '.cancelled-order-item', function (e) {
            let product_id = ($(this).attr('data-product-id'))
            $('#cancelKitItem').modal('hide')

            let hidden = `<input type="hidden" name="order-items[cancelled]" value="1"/>`
            $('.product-text').append(hidden);
            $('.kit-status-' + product_id).html('Cancelled').css("color", "red");
            $('.order-item-' + product_id).val('1')
            $('.cancelOrderKit'+product_id).hide();
        });

        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove()
            setOrderDetails()
        });

        $(document).on('change', '.quantity-input', function () {
            let input = parseInt($(this).closest('.number-input').find('.quantity-input').val())
            for (let i = parseInt($(this).attr('data-index'))+1; i <= parseInt($(this).attr('data-index')) + parseInt($(this).attr('data-child-count')); i++) {
                let childInput = $('.childquantity-input_'+i)
                childInput.val(childInput.attr('data-quantity') * input)
                setOrderDetails();
            }

            setOrderDetails();
        });

        $(document).on('keyup', '.quantity-input', function () {
            $(this).change();
        });

        $(document).on('click', 'button[data-fulfill-action]', function(event) {
            event.preventDefault();
            event.stopPropagation();

            let button = $(this);

            $('#order-fulfill-dialog .orderNumber').text(button.data('order-number'));
            $('#order-fulfill-dialog .mark-as-fulfilled-message').text(button.data('mark-as-fulfilled-message'));
            $('#order-fulfill-dialog #mark-as-fulfilled-form').attr('action', button.data('fulfill-action'));

            $('#order-fulfill-dialog').modal('show');
        });

        $(document).on('click', 'button[data-cancel-action]', function(event) {
            event.preventDefault();
            event.stopPropagation();

            let button = $(this);

            $('#order-cancellation-dialog .orderNumber').text(button.data('order-number'));
            $('#order-cancellation-dialog .cancellationMessage').text(button.data('cancellation-message'));
            $('#order-cancellation-dialog #cancellationForm').attr('action', button.data('cancel-action'));

            $('#order-cancellation-dialog').modal('show');
        });

        $(document).on('click', '.cancelOrderItem', function(event) {
            event.stopPropagation()
            let button = $(this);

            $('#order-item-cancellation-dialog .orderSkuNumber').text(button.data('order-sku-number'));
            $('#order-item-cancellation-dialog .orderNumber').text(button.data('order-number'));
            $('#order-item-cancellation-dialog .productName').text(button.data('product-name'));
            $('#order-item-cancellation-dialog #itemCancellationForm').attr('action', button.data('cancel-item-action'));

            $('#order-item-cancellation-dialog').modal('show');
        });

        function setOrderDetails() {
            let items = $('.parentProductRow');
            let total = 0;

            if (items.length) {
                $.map(items, function (elem) {
                    let row = $(elem);
                    let itemPrice = parseFloat(row.find('.price-value').html());
                    let itemCount = parseInt(row.find('.quantity-input').val());
                    let itemTotal = parseFloat(itemPrice * itemCount);
                    if (isNaN(itemTotal)) itemTotal = 0;
                    row.find('.item-total-price').html(itemTotal.toFixed(2))
                    total += parseFloat(itemTotal)
                })
            }
            if (isNaN(total)) total = 0;
            let orderShipping = parseFloat($('.total-table').find('.total-shipping').html());
            let orderDiscount = parseFloat($('.total-table').find('.total-discount').html());
            let orderTaxes = parseFloat($('.total-table').find('.total-taxes').html());
            let finalTotal = total + orderShipping + orderTaxes - orderDiscount;
            $(document).find('.subtotal-value').html(total.toFixed(2))
            $(document).find('.total-value').html(finalTotal.toFixed(2))
        }

        setOrderDetails()

        function checkForErrors() {
            let container = $(document).find('.form-error-messages').closest('.tab-pane')
            if (container.length) {
                container.each(function(key,elem) {
                    let parentContainerId = $(elem).attr('aria-labelledby');
                    let parentContainer = $(document).find('#' + parentContainerId).addClass('text-red')

                    if (key == 0) {
                        parentContainer.trigger('click')
                    }
                })
            }
        }

        checkForErrors()

        $('.import-orders').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.import-order-form');
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

                    window.dtInstances['#orders-table'].ajax.reload();
                },
                error: function (response) {
                    if (response.status != 504) {
                        $('#csv-filename').empty();

                        toastr.error('Invalid CSV data');

                        if (typeof response.responseJSON !== 'undefined') {
                            appendValidationMessages($('#import-orders-modal'), response);
                        }
                    }
                }
            });

            $('.import-order-form')[0].reset()
            $('#import-orders-modal').modal('hide');
            toastr.info('Order import started. You may continue using the system');
        });

        $('.export-orders').click(function () {
            $('#export-orders-modal').modal('toggle');
        })

        $('#orders-csv-button').on('change', function (e) {
            if (e.target.files) {
                if (e.target.files[0]) {
                    let filename = e.target.files[0].name
                    $('#csv-filename').append(
                        '<h5 class="heading-small">' +
                        'Filename: ' + filename +
                        '</h5>'
                    )
                }

                $('#import-orders-modal').focus()
            }
        })

        $(document).on('click', '.productForm .saveButton:not(#submit-button)', function(e) {
            e.preventDefault();

            $(this).addClass('d-none')
            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.productForm');

            _form.removeClass('editable')
            _form.find('.loading').removeClass('d-none')

            let form = _form[0];
            let formData = new FormData(form);

            if (_form.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                formData.append('tags[]', '')
            }

            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                enctype: 'multipart/form-data',
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    _form.find('.loading').addClass('d-none')
                    _form.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
                    _form.find('.notes-data span.notesText').html(data.order.notes)
                    _form.find('input[type="checkbox"]').each(function() {
                        let spanText = ''
                        if(this.checked) {
                            spanText = 'Yes'
                        }
                        $(this).closest('.editCheckbox').find('.checkbox-result').html(spanText)
                    })
                    toastr.success(data.message)
                },
                error: function (messages) {
                    _form.find('.loading').addClass('d-none')
                    _form.find('.saveError').removeClass('d-none').removeClass('d-none').css('display', 'block').fadeOut(5000)
                    _form.addClass('editable')

                    if (messages.responseJSON.errors) {
                        $.each(messages.responseJSON.errors, function (key, value) {
                            toastr.error(value)
                        });
                    }
                }
            });
        });

        $('.reship_check_all').click(function() {
            if ($('.reship_checkbox:checkbox:checked').length > 0) {
                $('input:checkbox[class=reship_checkbox]').prop('checked', false);
                $('.reship_submit').prop('disabled', true);

                $(this).html('Check All');
            } else {
                $('input:checkbox[class=reship_checkbox]').prop('checked', true);
                $('.reship_submit').prop('disabled', false);

                $(this).html('Uncheck All');
            }

        });

        $('.reship_checkbox').click(function() {
            if ($('.reship_checkbox:checkbox:checked').length > 0) {
                $('.reship_submit').prop('disabled', false);
                $('.reship_check_all').html('Uncheck All');
            } else {
                $('.reship_submit').prop('disabled', true);
                $('.reship_check_all').html('Check All');
            }
        });

        $('#showDynamicKitItems').on('show.bs.modal', function (e) {
            $('#showDynamicKitItems .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/order/getKitItems/' + itemId,

                success:function(data) {
                    $('#showDynamicKitItems>div').html(data);
                },
            });
        });

        $('#bulk-edit-modal').on('show.bs.modal', function () {
            let ids = []
            let form = $('#bulk-edit-form')
            $('#item-type').text('Orders')

            $('input[name^="bulk-edit"]').each(function() {
                if ($(this).prop('checked')) {
                    let orderId = $(this).data('id')

                    ids.push(parseInt(orderId))
                }
            })

            $('#number-of-selected-items').text(ids.length)
            $('#model-ids').val(ids)

            $.ajax({
                type: 'POST',
                serverSide: true,
                url: '/order/bulkOrderStatus',
                data: {
                    'ids': ids
                },
                success: function(data) {
                    const res = data.results

                    if (res.pending) {
                        $('#cancel-bulk-order').removeAttr('hidden')
                        $('#mark-as-fulfilled').removeAttr('hidden')
                    } else if (res.unfulfilled) {
                        $('#mark-as-fulfilled').removeAttr('hidden')
                        $('#cancel-bulk-order').attr('hidden', true)
                    } else {
                        $('#mark-as-fulfilled').attr('hidden', true)
                        $('#cancel-bulk-order').attr('hidden', true)
                    }
                }
            })

            form.attr('action', '/order/bulk-edit')
            form.serialize()
        });

        function setCountryCode(country) {
            $.ajax({
                type: 'GET',
                serverSide: true,
                url: '/site/filterCountries',
                data: {
                    'country': country
                },
                success: function(response) {
                    $('#cont_info_country_code').text(response.results.country_code)
                }
            })
        }

        setCountryCode($('#cont_info_country_name').text());

        $(document).on('hidden.bs.modal', '#shippingInformationEdit', function (e) {
            let contactInfoForm = $('.orderContactInfo');
            contactInfoForm.find('#cont_info_name').html($(this).find('#input-shipping_contact_information\\[name\\]').val());
            contactInfoForm.find('#cont_info_company_name').html($(this).find('#input-shipping_contact_information\\[company_name\\]').val());
            contactInfoForm.find('#cont_info_company_number').html($(this).find('#input-shipping_contact_information\\[company_number\\]').val());
            contactInfoForm.find('#cont_info_address').html($(this).find('#input-shipping_contact_information\\[address\\]').val());
            contactInfoForm.find('#cont_info_address2').html($(this).find('#input-shipping_contact_information\\[address2\\]').val());
            contactInfoForm.find('#cont_info_zip').html($(this).find('#input-shipping_contact_information\\[zip\\]').val());
            contactInfoForm.find('#cont_info_city').html($(this).find('#input-shipping_contact_information\\[city\\]').val());
            contactInfoForm.find('#cont_info_email').html($(this).find('#input-shipping_contact_information\\[email\\]').val());
            contactInfoForm.find('#cont_info_phone').html($(this).find('#input-shipping_contact_information\\[phone\\]').val());
            contactInfoForm.find('#cont_info_country_name').text($(this).find('[name="shipping_contact_information[country_id]"]').select2('data')[0].text);

            setCountryCode(contactInfoForm.find('#cont_info_country_name').text())
        });

        $('.shipping_contact_info_set').on('click', function(e) {
            toastr.success('Shipping information saved.');
        });

        $('#shippingInformationEdit [type="button"]').on('click', function(e) {
            e.preventDefault();
            $('#shippingInformationEdit').modal('hide');
        });

        $('#submit-bulk-order-edit').click(function (e) {
            e.preventDefault()
            e.stopPropagation()

            let modal = $('#bulk-edit-modal')
            let form = $('#bulk-edit-form')

            let formData = new FormData(form[0])

            $.ajax({
                type: 'POST',
                url: form[0].action,
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    modal.modal('toggle')

                    $('#bulk-edit-btn').attr('hidden', true)

                    $('#select-all-checkboxes').prop('checked', false)

                    $('.bulk-edit-tags').empty()
                    $('#input-country_id').empty()

                    form[0].reset()

                    toastr.success('Updated successfully!')

                    window.dtInstances['#orders-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(modal, response)
                }
            })
        })

        $('#orders-table').on('draw.dt', function() {
            const selectAllCheckboxes = $('#select-all-checkboxes')
            const datatableCheckboxes = $('.custom-datatable-checkbox')

            if (selectAllCheckboxes.prop('checked')) {
                datatableCheckboxes.each(function (i, element) {
                    element.checked = true
                })
            } else {
                datatableCheckboxes.each(function (i, element) {
                    element.checked = false
                })
            }
        })

        $('#cancel-bulk-order').on('click', function () {
            let _form = $(this).closest('#bulk-edit-form');
            let form = _form[0];
            let formData = new FormData(form);

            app.confirm('Bulk cancel orders', 'Are you sure you want to cancel these orders?', () => {
                $.ajax({
                    type: 'POST',
                    url: '/order/bulk-cancel',
                    enctype: 'multipart/form-data',
                    headers: {'X-CSRF-TOKEN': formData.get('_token')},
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $('#bulk-edit-modal').modal('toggle')

                        toastr.success('Orders were cancelled successfully')

                        window.dtInstances['#orders-table'].ajax.reload()
                    },
                    error: function () {
                        toastr.error('Unable to cancel orders')
                    }
                })
            })
        })

        $('#mark-as-fulfilled').on('click', function () {
            let _form = $(this).closest('#bulk-edit-form');
            let form = _form[0];
            let formData = new FormData(form);

            app.confirm('Mark orders as fulfilled', 'Are you sure you want to mark these orders as fulfilled?', () => {
                $.ajax({
                    type: 'POST',
                    url: '/order/bulk-mark-as-fulfilled',
                    enctype: 'multipart/form-data',
                    headers: {'X-CSRF-TOKEN': formData.get('_token')},
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $('#bulk-edit-modal').modal('toggle')

                        toastr.success('Orders were marked as fulfilled successfully')

                        window.dtInstances['#orders-table'].ajax.reload()
                    },
                    error: function () {
                        toastr.error('Unable to fulfill selected orders')
                    }
                })
            })
        })

        $('.billing_contact_information-country_id').val($('.billing_contact_information-country_id').data('value')).trigger('change')
        $('.shipping_contact_information-country_id').val($('.shipping_contact_information-country_id').data('value')).trigger('change')
    });

    $(document).ready(function () {
        auditLog();
    })

    $('.confirmation').on('click', function () {
        let button = $(this);
        let route = $(this).data('route');

        app.confirm(null, 'Are you sure you want to void this label?', () => {
            $.ajax({
                type: 'POST',
                serverSide: true,
                url: route,
                success: function (data) {
                    if (data.success) {
                        toastr.success(data.message);
                        button.hide();
                        window.location.reload()
                    } else {
                        toastr.warning(data.message);
                    }
                },
                error: function () {
                    toastr.warning('Something went wrong!')
                }
            });
        });
    });
};
