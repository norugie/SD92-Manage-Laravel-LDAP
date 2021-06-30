(function() {
    $('.enable-accounts').attr('disabled', true);

    $('#inactive_employee_table').on("change", ".employee-checkbox", function() {
        var employee = '#employee_checkbox_' + $(this).val();
        var employeeUsername = $(employee).val();
        var employeeFullname = $(employee).data('name');
        var employeeEnableUsernameList = $('#employee_enable').val();

        if (employeeEnableUsernameList.includes(employeeUsername + ',')) {
            employeeEnableUsernameList = employeeEnableUsernameList.replace(employeeUsername + ',', '');
            $('#employee-to-enable_' + employeeUsername).remove();
        } else {
            employeeEnableUsernameList = employeeEnableUsernameList + employeeUsername + ',';
            $('#employees-to-enable').append('<li id="employee-to-enable_' + employeeUsername + '">' + employeeFullname + '</li>');
        }

        $('#employee_enable').attr('value', employeeEnableUsernameList);

        if ($('#employee_enable').val().length === 0 ?
            $('.enable-accounts').attr('disabled', true).removeAttr('data-toggle').removeAttr('data-target') :
            $('.enable-accounts').removeAttr('disabled').attr('data-toggle', 'modal').attr('data-target', '#enableAccounts')
        );
    });
})();