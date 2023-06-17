window.TaskForm = function () {
    $(document).ready(function() {
        $('#task-table').DataTable(
            {
                serverSide: true,
                ajax: '/task/data-table',
                responsive: true,
                initComplete: function()
                {
                    var dtable = $("#task-table").dataTable().api()
                    $(".dataTables_filter input")
                        .unbind() // Unbind previous default bindings
                        .bind("input", function(e) { // Bind our desired behavior
                            // If the length is 3 or more characters, or the user pressed ENTER, search
                            if(this.value.length >= 3) {
                                // Call the API search function
                                dtable.search(this.value).draw();
                            }
                            // Ensure we clear the search if they backspace far enough
                            if(this.value == "") {
                                dtable.search("").draw();
                            }
                            return;
                        });
                },
                order: [1, 'desc'],
                columns: [
                    {
                        'non_hiddable': true,
                        "orderable": false,
                        "class": "text-left",
                        "title": "",
                        "data": function (data) {
                            let editButton = '<a href="' + data['link_edit'] + '" class="btn btn-primary edit" style="display: inline-block"> Edit </a>';

                            return editButton
                        },
                    },
                    {
                        "title": "User",
                        "data": function (data) {
                            return '<a href="' + data.user['url'] + '">' + data.user['name'] + '</a>'
                        },
                        "name": "user_contact_information.name"
                    },
                    {
                        "title": "Customer",
                        "data": function (data) {
                            return '<a href="' + data.customer['url'] + '">' + data.customer['name'] + '</a>'
                        },
                        "name": "customer_contact_information.name"
                    },
                    {
                        "title": "Task Type",
                        "name": "task_types.name",
                        "data": "task_type"
                    },
                    {

                        "title": "Notes",
                        "name": "notes",
                        "data": "notes"
                    },
                    {
                        'non_hiddable': true,
                        "orderable": false,
                        "class": "text-left",
                        "title": "",
                        "data": function (data) {
                            let deleteButton = app.tableDeleteButton(
                                `Are you sure you want to delete ${data.task_type}?`,
                                data.link_delete
                            );

                            return deleteButton;
                        }
                    },
                ],
            });

        $(document).on('click', '#task-table tbody tr', function (event) {
            if( document.getSelection().toString() === '' ) {
                window.location.href = $(event.target).parent().find('.edit').attr('href')
            }
        });

    });
};
