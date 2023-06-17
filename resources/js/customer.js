window.Customer = function () {
    window.datatables.push({
        selector: '#customers-table',
        resource: 'customers',
        ajax: '/customer/data-table',
        columns: [
            {
                "orderable": false,
                "class":"text-left",
                "title": "",
                "data": function (data) {
                    return `<a type="button" class="table-icon-button" href="${data['link_edit']}">
                                <i class="picon-edit-filled icon-lg" title="Edit"></i>
                            </a>`;
                },
            },
            {"title": "Name", "data": "name", "name": "contact_informations.name"},
            {"title": "Company Name", "data": "company_name", "name": "contact_informations.company_name"},
            {"title": "Address", "data": "address", "name": "contact_informations.address"},
            {"title": "Address2", "data": "address2", "name": "contact_informations.address2"},
            {"title": "Zip", "data": "zip", "name": "contact_informations.zip"},
            {"title": "City", "data": "city", "name": "contact_informations.city"},
            {"title": "Email", "data": "email", "name": "contact_informations.email"},
            {"title": "Phone", "data": "phone", "name": "contact_informations.phone"},
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            }
        ],
    })
}
