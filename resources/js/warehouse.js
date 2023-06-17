window.Warehouse = function () {

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#warehouses-table',
        resource: 'warehouses',
        ajax: {
            url: '/warehouses/data-table'
        },
        columns: [
            {
                "title": "Name",
                "name": "contact_informations.name",
                "data": function (data) {
                    return `
                        <a href="#" data-id="${data.id}" data-toggle="modal" data-target="#warehouseEditModal">
                            ${data.warehouse_name}
                        </a>
                    `
                }
            },
            {"title": "Company", "data": "warehouse_company_name", "name": "contact_informations.company_name"},
            {"title": "Address", "data": "warehouse_address", "name": "contact_informations.address"},
            {"title": "City", "data": "warehouse_city", "name": "contact_informations.city"},
            {"title": "State", "data": "warehouse_state", "name": "contact_informations.state"},
            {"title": "ZIP", "data": "warehouse_zip", "name": "contact_informations.zip"},
            {"title": "Country", "data": "warehouse_country", "name": "contact_informations.country.iso_3166_2"},
            {"title": "Email", "data": "warehouse_email", "name": "contact_informations.email"},
            {"title": "Phone", "data": "warehouse_phone", "name": "contact_informations.phone"},
            {
                "title": "Customer Name",
                "data": function (data) {
                    return data.customer['name']
                },
                "name": "customer_contact_information.name"
            },
            {
                'non_hiddable': true,
                'orderable': false,
                'class': 'text-center',
                'title': '',
                'data': function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.warehouse_name}?`,
                        data.link_delete
                    );
                }
            },
        ],
        dropdownAutoWidth : true,
    })

    $(document).ready(function() {
        $(document).find('select:not(.custom-select)').select2();

        $('#warehouseEditModal').on('show.bs.modal', function (e) {
            $('#warehouseEditModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/warehouses/getWarehouseModal/' + itemId,
                success:function(data) {
                    $('#warehouseEditModal > div')
                        .html(data)
                        .find('select')
                        .select2()
                },
                error: function (response) {
                    let modal = $('#warehouseEditModal');

                    appendValidationMessages(modal, response)
                }
            });
        })

        $('#warehouseCreateModal').on('show.bs.modal', function (e) {
            $('#warehouseCreateModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/warehouses/getWarehouseModal',
                success:function(data) {
                    $('#warehouseCreateModal > div')
                        .html(data)
                        .find('select')
                        .select2()
                },
                error: function (response) {
                    let modal = $('#warehouseCreateModal');

                    appendValidationMessages(modal, response)
                }
            });
        })

        $('#warehouseDeleteModal').on('show.bs.modal', function (e) {
            let deleteURL = $(e.relatedTarget).data('url')
            let token = $(e.relatedTarget).data('token')

            $('#deleteWarehouseForm').attr('action', deleteURL)
            $('#formToken').attr('value', token)
        })

        $('.deleteWarehouse').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('#deleteWarehouseForm');
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
                    $('#warehouseDeleteModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#warehouses-table'].ajax.reload()
                }
            });
        });
    });
};
