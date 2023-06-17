window.ProductLocation = function () {
    $(document).find('select:not(.custom-select)').select2();
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)

    window.datatables.push({
        selector: '#product-location-table',
        resource: 'location_product',
        ajax: {
            url: '/locations/product/data-table',
            data: function (data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['location_product'] = data
            }
        },
        columns: [
            {
                "title": "",
                "data": "id",
                "name": "locations.id",
                "visible": false
            },
            {
                "title": "Location",
                "data": "location",
                "name": "locations.name"
            },
            {
                "orderable": false,
                "title": "Warehouse",
                "data": "warehouse",
                "name": "warehouse"
            },
            {
                "title": "SKU",
                "data": "sku",
                "name": "products.sku"
            },
            {
                "title": "Product name",
                "data": "product_name",
                "name": "products.name"
            },
            {
                "title": "Quantity",
                "data": "quantity",
                "name": "location_product.quantity_on_hand",
                "className": "quantity_editable"
            },
            {
                "title": "Pickable",
                "name": "locations.pickable",
                "data": function (data) {
                    return data.location_pickable;
                },
            },
            {
                "title": "Sellable",
                "name": "locations.sellable",
                "data": function (data) {
                    return data.location_sellable;
                },
            }
        ],
        createdRow: function(row, data, dataIndex){
            $('td:eq(0)', row).css('min-width', '100px');
        },
        dropdownAutoWidth : true,
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td.quantity_editable')
                    .attr("data-product-id", data.product_id)
                    .attr("data-location-id", data.location_id)
                    .attr("data-location-product-id", data.location_product_id)
                    .attr("data-quantity-on-hand", data.quantity)
                    .attr('title', 'Edit quantity')
                    .css('min-width', '100px');
        }
    })

    $(document).ready(function() {
        $('.warehouse_id').select2({
            dropdownParent: $("#import-inventory-modal")
        });

        $('.importInventory').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.importInventoryForm');
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
                    $('#csv-filename').empty()

                    toastr.success(data.message)

                    window.dtInstances['#product-location-table'].ajax.reload()
                },
                error: function (response) {
                    if (response.status != 504) {
                        if (response.status === 403) {
                            toastr.error('Action is not permitted for this customer')
                        } else {
                            toastr.error('Invalid CSV data')

                            $('#csv-filename').empty()

                            appendValidationMessages(modal, response);
                        }
                    }
                }
            });

            $('#import-inventory-modal').modal('hide');
            toastr.info('Inventory import started. You may continue using the system');
        });

        $('#InventoryCsvButton').on('change', function (e) {
           if (e.target.files) {
               if (e.target.files[0]) {
                   let filename = e.target.files[0].name
                   $('#csv-filename').append(
                       '<h5 class="heading-small">' +
                       'Filename: ' + filename +
                       '</h5>'
                   )
               }

               $('#import-inventory-modal').focus()
           }
        })

        let getProductId;
        let getLocationId;
        let getLocationProductId;
        let putPreviousValue;
        let storeNewValue;
        $(document).on("dblclick", "tr td.quantity_editable", function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $this = $(this);
            const val = $this.text();
            getProductId = $(this).data("product-id");
            getLocationId = $(this).data("location-id");
            getLocationProductId = $(this).data("location-product-id");
            $this.toggleClass("show");
            if ($this.hasClass("show")) {
                $this.empty();
                const input =
                    '<input type="number" class="form-control locationQuantity form-control font-weight-600 text-black h-auto editfield">';
                const inputGroup =
                    '<div class="input-group input-group-alternative input-group-merge">' +
                    input +
                    "</div>";
                $(inputGroup).appendTo($this).find("input").val(val).focus();
            } else {
                let value = $this.data("quantity-on-hand");
                $this.text(value);
                $this.find(".editfield").remove();
            }
        });

        putPreviousValue = function () {
            $("tr td.quantity_editable").each(function(){
                $this = $(this);
                // get previous value
                const val = $this.data("quantity-on-hand");
                $this.empty().html(val);
                $this.removeClass('show');
            });
        };

        storeNewValue = function () {
            $("tr td .editfield").each(function(){
                $this = $(this);
                const val = $this.val();
                const $td = $this.closest("td");
                if (val === $td.data("quantity-on-hand")) {
                    $td.empty().html(val);
                    $td.removeClass("show");
                    return;
                }
                const locationProduct = [
                    {
                        product_id: getProductId,
                        quantity_on_hand: val,
                        location_product_id: getLocationProductId,
                    },
                ];
                $.ajax({
                    url:
                        "/location/product/" +
                        getLocationId +
                        "/quantity/update",
                    type: "post",
                    data: {
                        location_product: locationProduct,
                    },
                    dataType: "json",
                    success: function (data) {
                        $td.data("quantity-on-hand", val);
                        $td.empty().html(val);
                        $td.removeClass("show");
                        toastr.success(data.message);
                    },
                    error: function (messages) {
                        if (messages.responseJSON.errors) {
                            $.each(
                                messages.responseJSON.errors,
                                function (key, value) {
                                    toastr.error(value);
                                }
                            );
                        }
                    },
                });
            });
        };

        $(document).click(function (e) {
            putPreviousValue();
        });

        $(document).on('keypress',function(e) {
            // keypress: enter
            if(e.which === 13) {
                storeNewValue();
            }
        });

        $('.export-inventory').click(function () {
            $('#export-inventory-modal').modal('toggle')
        });

        let customerSelect = $('.customer_id');
        let warehouseSelect = $('.enabled-for-customer[name="warehouse_id"]');

        function toggleInputs() {
            if (!customerSelect.val()) {
                warehouseSelect.prop('disabled', true);
                warehouseSelect.append(new Option('Select', 'title', true, false));

                if (warehouseSelect.left > 0) {
                    warehouseSelect[0].options[0].disabled = true;
                }
            } else {
                warehouseSelect.prop('disabled', false);
            }
        }

        customerSelect.on('change', function () {
            let customerId = customerSelect.val();
            let selectedWarehouse = warehouseSelect.val();

            warehouseSelect.empty();

            toggleInputs();

            if (customerId) {
                $.get('/purchase_orders/filterWarehouses/' + customerId, function(data) {
                    $.map(data.results, function(result) {
                        if (!warehouseSelect.find(`option[value="${result.id}"]`).length) {
                            let selected = Number(result.id) == Number(selectedWarehouse);
                            warehouseSelect.append(new Option(result.text, result.id, selected, selected));
                        }
                    })
                });
            }
        }).trigger('change');
    });
};
