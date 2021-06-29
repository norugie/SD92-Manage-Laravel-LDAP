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
            text: 'MOVE ACCOUNTS',
            className: 'btn bg-blue waves-effect move-accounts'
        },
        {
            text: 'DISABLE ACCOUNTS',
            className: 'btn bg-red waves-effect disable-accounts'
        }
    ]
});

$('#inactive_employee_table').DataTable({
    "bSort": true,
    "lengthChange": false,
    "iDisplayLength": 10,
    "columnDefs": [{
        "targets": [3],
        "searchable": false,
        "orderable": false,
        "visible": true
    }]
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