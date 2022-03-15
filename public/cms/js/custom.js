(function() {
    // Custom JS DataTable
    $('#employee_table').DataTable({
        "bSort": true,
        'aaSorting': [
            [1, 'asc']
        ],
        "lengthChange": false,
        "iDisplayLength": 10,
        "columnDefs": [{
            "targets": [0, 5],
            "searchable": false,
            "orderable": false,
            "visible": true
        }],
        "dom": 'Bfrtip',
        "buttons": [{
                text: '<i class="material-icons">redo</i><span>MOVE</span>',
                className: 'btn bg-blue waves-effect table-buttons move-accounts'
            },
            {
                text: '<i class="material-icons">do_not_disturb</i><span>DISABLE</span>',
                className: 'btn bg-red waves-effect table-buttons disable-accounts'
            }
        ]
    });

    $('#inactive_employee_table').DataTable({
        "bSort": true,
        'aaSorting': [
            [1, 'asc']
        ],
        "lengthChange": false,
        "iDisplayLength": 10,
        "columnDefs": [{
            "targets": [0, 4],
            "searchable": false,
            "orderable": false,
            "visible": true
        }],
        "dom": 'Bfrtip',
        "buttons": [{
            text: '<i class="material-icons">check_circle</i><span>ENABLE</span>',
            className: 'btn bg-blue waves-effect table-buttons enable-accounts'
        }]
    });

    $('#log_table').DataTable({
        "bSort": true,
        "lengthChange": false,
        "iDisplayLength": 10,
        'aaSorting': [
            [2, 'desc']
        ]
    });

    $('#student_table').DataTable({
        "bSort": true,
        "lengthChange": false,
        "iDisplayLength": 10,
        'aaSorting': [
            [0, 'asc']
        ]
    });

    // Custom JQuery Validator
    $('.new_form_validate').validate({
        unhighlight: function(input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function(error, element) {
            $(element).parents('.form-group').append(error);
        }
    });

    $('.edit_form_validate').validate({
        unhighlight: function(input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function(error, element) {
            $(element).parents('.form-group').append(error);
        }
    });

    // Custom JQuery Validator for Users page
    $('.new_user_validate').validate({
        rules: {
            'username': {
                required: true,
                alreadyExists: true
            },
            'department': {
                required: true
            }
        },
        unhighlight: function(input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function(error, element) {
            $(element).parents('.form-group').append(error);
        }
    });
})();