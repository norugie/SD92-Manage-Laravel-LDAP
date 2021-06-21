// Custom JS DataTable
$('.js-basic-example').DataTable({
    "bSort": true,
    "lengthChange": false,
    "iDisplayLength": 10
});

// Custom JQuery Validator
$('.new_form_validate').validate({
    unhighlight: function (input) {
        $(input).parents('.form-line').removeClass('error');
    },
    errorPlacement: function (error, element) {
        $(element).parents('.form-group').append(error);
    }
});

$('.edit_form_validate').validate({
    unhighlight: function (input) {
        $(input).parents('.form-line').removeClass('error');
    },
    errorPlacement: function (error, element) {
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
    unhighlight: function (input) {
        $(input).parents('.form-line').removeClass('error');
    },
    errorPlacement: function (error, element) {
        $(element).parents('.form-group').append(error);
    }
});