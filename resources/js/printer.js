window.Printer = function() {

    const filterForm = $('#toggleFilterForm').find('form');

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#printers-table',
        resource: 'printers',
        ajax: {
            url: '/printer/data-table',
            data: function (data) {
                let request = {}
                filterForm
                    .serializeArray()
                    .map(function(input) {
                        request[input.name] = input.value;
                    });

                data.filter_form = request
                data.from_date = $('#printers-table-date-filter').val();
            }
        },
        order: [1, 'desc'],
        columns: [
            {
                "orderable": false,
                "class": "text-left",
                "title": "",
                "data": function (data) {
                    let jobsButton = '<a href="' + data['link_jobs'] + '" class="pr-4">' +
                        '<i class="picon-printer-light icon-lg " title="Jobs"></i>' +
                        '</a>';

                    let disableButton = '<a href="' + data['link_disable'] + '">' +
                        '<i class="picon-stop-light icon-lg" title="Disable"></i>' +
                        '</a>';

                    let enableButton = '<a href="' + data['link_enable'] + '">' +
                        '<i class="picon-repeat-light icon-lg" title="Enable"></i>' +
                        '</a>';

                    if (data.disabled_at == null) {
                        return jobsButton + disableButton;
                    } else {
                        return jobsButton + enableButton;
                    }
                },
            },
            {
                "title": "ID",
                "data": "id",
                "name": "id"
            },
            {
                "title": "Hostname",
                "data": "hostname",
                "name": "hostname"
            },
            {
                "title": "Name",
                "data": "name",
                "name": "name"
            },
            {
                "title": "Date added",
                "data": "created_at",
                "name": "created_at"
            },

        ],
    });
};
