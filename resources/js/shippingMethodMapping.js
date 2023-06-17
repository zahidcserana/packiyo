window.ShippingMethodMapping = function () {
    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#shipping-method-mapping-table',
        resource: 'shipping_method_mappings',
        ajax: {
            url: '/shipping_method_mapping/data-table'
        },
        order: [1, 'desc'],
        columns: [
            {
                "non_hiddable": true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "edit",
                "data": function (data) {
                    return `
                        <a type="button" class="table-icon-button" href="${data.link_edit}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </a>`
                },
            },
            {
                "title": "Shop Shipping Method",
                "name": "shipping_method_name",
                "data": "shipping_method_name",
            },
            {
                "title": "Carrier",
                "name": "shipping_carriers.name",
                "data": "carrier_name"
            },
            {
                "title": "Method",
                "name": "shipping_methods.name",
                "data": "method_name"
            },
            {
                "title": "Return Carrier",
                "name": "return_shipping_carriers.name",
                "data": "return_carrier_name"
            },
            {
                "title": "Return Method",
                "name": "return_shipping_methods.name",
                "data": "return_method_name"
            },
            {
                "title": "Mapped",
                "name": "shipping_method_mappings.shipping_method_name",
                "data": "is_mapped"
            },
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-right",
                "title": "",
                "name": "trash",
                "data": function (data) {
                    if (data.link_delete != null) {
                        return app.tableDeleteButton(
                            `Are you sure you want to delete ${data.shipping_method_name}?`,
                            data.link_delete
                        );
                    }

                    return null;
                }
            },
        ],
    });

    $(document).ready(function() {
        let customerSelect = $('.customer_id');
        let enabledForCustomer = $('.enabled-for-customer');
        let shippingMethodSelect = $('.shipping_method_id');

        function toggleInputs(){
            if (customerSelect.val() === '' || customerSelect.val() ===  null) {
                enabledForCustomer.prop('disabled', true);
                shippingMethodSelect.empty();
            } else {
                enabledForCustomer.prop('disabled', false);
            }
        }

        function changeSelectInputUrlAjax(selectInputToChange, value) {
            if (selectInputToChange && selectInputToChange.data('ajax--url')) {
                selectInputToChange.select2('destroy');
                selectInputToChange.data('ajax--url', selectInputToChange.data('ajax--url').replace(/\/\w+?$/, '/' + value));
                selectInputToChange.select2();
            }
        }

        customerSelect.on('select2:select', function () {
            shippingMethodSelect.empty();
        });

        customerSelect.on('change', function (event) {
            let customerId = customerSelect.val();

            toggleInputs();

            if (customerId) {
                changeSelectInputUrlAjax(shippingMethodSelect, customerId);

                customerSelect.trigger('ajaxSelectOldValueUrl:toggle');
            }
        }).trigger('change');
    });
}
