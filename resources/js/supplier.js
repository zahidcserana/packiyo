window.Supplier = function () {
    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#supplier-table',
        resource: 'suppliers',
        ajax: {
            url: '/supplier/data-table'
        },
        columns: [
            {
                "orderable": false,
                "title": "",
                "class": "text-left",
                "data": function (data) {
                    return `
                        <button type="button" class="table-icon-button" data-id="${data.link_edit}" data-toggle="modal" data-target="#vendorEditModal">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </button>
                    `
                },
            },
            {
                "title": "Name",
                "data": "supplier_name",
                "name": "contact_informations.name"
            },
            {
                "title": "Address",
                "data": "supplier_address",
                "name": "contact_informations.address"
            },
            {
                "title": "Zip", "data":
                    "supplier_zip",
                "name": "contact_informations.zip"
            },
            {
                "title": "City",
                "data": "supplier_city",
                "name": "contact_informations.city"
            },
            {
                "title": "Email",
                "data": "supplier_email",
                "name": "contact_informations.email"
            },
            {
                "title": "Phone",
                "data": "supplier_phone",
                "name": "contact_informations.phone"
            },
            {
                "title": "Customer",
                "name": "customer_contact_information.name",
                "data": function (data) {
                    return data.customer['name']
                }
            }
        ],
        dropdownAutoWidth : true,
    })

    $(document).ready(function() {
        $(document).find('select:not(.custom-select)').select2();

        function openCreationModal() {
            let hash = window.location.hash;

            if (hash && hash === '#open-modal') {
                $(document).find('#supplierCreateModal').modal('show')

                window.location.hash = '';
            }
        }

        openCreationModal();

        $(document).on('click', '.openPurchaseOrderCreateModal', function () {
            openCreationModal();
        })

        $(window).on('hashchange', function (e) {
            openCreationModal();
        });

        $('.modal-create-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('span.invalid-feedback').remove()

            let _form = $(this).closest('.supplierForm');
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
                    form.reset()

                    $('#supplierCreateModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#supplier-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages($('#supplierCreateModal'), response)
                }
            });
        });

        $('#supplierCreateModal').on('show.bs.modal', function (e) {
            $('#supplierCreateModal select').each((i, element) => {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).select2('destroy');
                }

                $(element).select2();
            });
        });

        $('#vendorEditModal').on('show.bs.modal', function (e) {
            $('#vendorEditModal .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/supplier/getVendorModal/' + itemId,

                success: function(data) {
                    $('#vendorEditModal > div').html(data);

                    $('#vendorEditModal select').each((i, element) => {
                        if ($(element).hasClass('select2-hidden-accessible')) {
                            $(element).select2('destroy');
                        }

                        $(element).select2();
                    });
                },
                error: function (response) {
                    let modal = $('#vendorEditModal');

                    appendValidationMessages(modal, response)
                }
            });
        })

        $('.deleteVendor').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('#deleteVendorForm');
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
                    $('#vendorDeleteModal').modal('toggle');
                    $('#vendorEditModal').modal('toggle');

                    toastr.success(data.message)

                    window.dtInstances['#supplier-table'].ajax.reload()
                }
            });
        });
    });
};
