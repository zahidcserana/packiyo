window.LocationType = () => {
    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#location-type-table',
        resource: 'location-type',
        ajax: {
            url: '/location/types/data-table',
            data: function (data) {
                data.from_date = $('#location-type-table-date-filter').val()
            }
        },
        order: [1, 'desc'],
        columns: [
            {
                "non_hiddable": true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "id",
                "data": function (data) {
                    return `
                        <button class="table-icon-button" type="button" onclick="window.location.href='${data.link_edit}'" data-id="${data.id}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </button>`
                },
            },
            {
                "title": "Name",
                "data": "name",
                "name": "name",
            },
            {
                "title": "Pickable",
                "data": "pickable",
                "name": "pickable",
            },
            {
                "title": "Disabled on picking app",
                "data": "disabled_on_picking_app",
                "name": "disabled_on_picking_app",
            },
            {
                "title": "Sellable",
                "data": "sellable",
                "name": "sellable",
            },
            {
                "title": "Customer",
                "data": function (data) {
                    return '<a href="' + data.customer.url + '" class="text-neutral-text-gray">' + data.customer.name + '</a>'
                },
                "name": "customer_contact_information.name",
                "class": "text-neutral-text-gray"
            },
            {
                "non_hiddable": true,
                "orderable": false,
                "class": "text-right",
                "title": "",
                "name": "action",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                },
            }
        ]
    })
}
