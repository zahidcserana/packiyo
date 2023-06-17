window.Activity = function () {
    window.datatables.push({
        selector: '#activity-table',
        resource: 'revisions',
        ajax: '/profile/activity/data-table',
        order: [0, 'desc'],
        columns: [
            {
                "title": "Created",
                "name": "revisions.created_at",
                "data": "created"
            },
            {
                "title": "Type",
                "name": "revisions.revisionable_type",
                "data": "type"
            },
            {
                "orderable": false,
                "title": "Note",
                "name": "revisions.key",
                "data": "note"
            }
        ]
    })
}
