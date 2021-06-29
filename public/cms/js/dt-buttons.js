(function() {
    $('.move-accounts').attr('disabled', true);
    $('.disable-accounts').attr('disabled', true);

    $('#employee_table').on("change", ".employee-checkbox", function() {
        var employee = '#employee_checkbox_' + $(this).val();
        var employeeUsername = $(employee).val();
        var employeeFullname = $(employee).data('name');
        var employeeUsernameList = $('#employee_multiple').val();
        var employeeFullnameList = $('#employee_multiple_name').val();
        if (employeeUsernameList.includes(employeeUsername + ',') && employeeFullnameList.includes(employeeFullname + ',')) {
            employeeUsernameList = employeeUsernameList.replace(employeeUsername + ',', '');
            employeeFullnameList = employeeFullnameList.replace(employeeFullname + ',', '');
            $('#employee-to-move_' + employeeUsername).remove();
        } else {
            employeeUsernameList = employeeUsernameList + employeeUsername + ',';
            employeeFullnameList = employeeFullnameList + employeeFullname + ',';
            $('#employees-to-move').append('<li id="employee-to-move_' + employeeUsername + '">' + employeeFullname + '</li>');
        }

        $('#employee_multiple').attr('value', employeeUsernameList);
        $('#employee_multiple_name').attr('value', employeeFullnameList);

        if ($('#employee_multiple').val().length === 0 ? $('.move-accounts').attr('disabled', true).removeAttr('data-toggle').removeAttr('data-target') : $('.move-accounts').removeAttr('disabled').attr('data-toggle', 'modal').attr('data-target', '#moveAccounts'));
    });
})();