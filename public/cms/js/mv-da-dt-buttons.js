(function() {
    $('#employee_table').on("change", ".employee-checkbox", function() {

        var employee = '#employee_checkbox_' + $(this).val();
        var employeeUsername = $(employee).val();
        var employeeFullname = $(employee).data('name');
        var employeeMoveUsernameList = $('#employee_multiple').val();
        var employeeDisableUsernameList = $('#employee_disable').val();

        if ($(employee).prop('checked')) {
            employeeMoveUsernameList = employeeMoveUsernameList + employeeUsername + ',';
            employeeDisableUsernameList = employeeDisableUsernameList + employeeUsername + ',';
            $('#employees-to-move').append('<li id="employee-to-move_' + employeeUsername + '">' + employeeFullname + '</li>');
            $('#employees-to-disable').append('<li id="employee-to-disable_' + employeeUsername + '">' + employeeFullname + '</li>');
            console.log(employeeMoveUsernameList);
        } else {
            employeeMoveUsernameList = employeeMoveUsernameList.replace(employeeUsername + ',', '');
            employeeDisableUsernameList = employeeDisableUsernameList.replace(employeeUsername + ',', '');
            $('#employee-to-move_' + employeeUsername).remove();
            $('#employee-to-disable_' + employeeUsername).remove();
            console.log(employeeMoveUsernameList);
        }

        $('#employee_multiple').prop('value', employeeMoveUsernameList);
        $('#employee_disable').prop('value', employeeDisableUsernameList);

        if ($('#employee_multiple').val().length === 0 || $('#employee_multiple').val().length === 0) {
            $('.move-accounts').attr('disabled', true).removeAttr('data-toggle').removeAttr('data-target');
            $('.disable-accounts').attr('disabled', true).removeAttr('data-toggle').removeAttr('data-target');
        } else {
            $('.move-accounts').removeAttr('disabled').attr('data-toggle', 'modal').attr('data-target', '#moveAccounts');
            $('.disable-accounts').removeAttr('disabled').attr('data-toggle', 'modal').attr('data-target', '#disableAccounts');
        }
    });
})();