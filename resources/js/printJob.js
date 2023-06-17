window.PrintJob = function (printerId) {

    const filterForm = $('#toggleFilterForm').find('form');

    window.datatables.push(
        {
            selector: '#jobs-table',
            resource: 'jobs',
            ajax:{
                "url":  '/printer/' + printerId + '/jobs-data-table/',
                data: function (data) {
                    let request = {};
                    filterForm
                        .serializeArray()
                        .map(function(input) {
                            request[input.name] = input.value;
                        });

                    data.filter_form = request;
                    data.from_date = $('#jobs-table-date-filter').val();
                }
            },
            order: [0, 'desc'],
            columns: [
                {
                    "title": "ID",
                    "data": "id",
                    "name": "id"
                },
                {
                    "title": "File",
                    "data": function (data) {

                        return '<a href="' + data['file'] + '" target="_blank">' + data['file'] + '</a>';
                    },
                    "name": "file"
                },
                {
                    "title": "Status",
                    "data": "status",
                    "name": "status"
                },
                {
                    "title": "User",
                    "data": "user",
                    "name": "user"
                },
                {
                    "title": "Printer",
                    "data": "printer",
                    "name": "printer"
                },
                {
                    "title": "Job start",
                    "data": "job_start",
                    "name": "job_start"
                },
                {
                    "title": "Job end",
                    "data": "job_end",
                    "name": "job_end"
                },
                {
                    "orderable": false,
                    "class": "text-left",
                    "title": "",
                    "data": function (data) {
                        let repeatButton = '<a href="' + data['link_repeat'] + '" class="btn btn-primary edit" style="display: inline-block"> Repeat </a>';
                        return (repeatButton);
                    },
                },
            ],
        }
    );
};
