(function() {
    var config = "/cms/config.json";

    $('#employee_department').change(function() {
        $('#employee_roles').empty();
        setRoles();
        setExtraRoles();
    });

    function setRoles() {
        var optgroup = '<optgroup label="General Roles">';
        $.getJSON(config, function(data) {
            $.each(data['global_roles'], function(key, value) {
                optgroup += '<option value="' + key + '">' + value + '</option>';
            });
            optgroup += "</optgroup>";
            $('#employee_roles').append(optgroup);
            $.AdminBSB.select.refresh();
        });
    }

    function setExtraRoles() {
        var local = $('#employee_department').val();

        var optgrouproles = '<optgroup label="' + local + ' Specific Roles">';
        var optgroupdept = '<optgroup label="' + local + ' Sub-Departments">';

        $.getJSON(config, function(data) {
            $.each(data['locations'][local]['local_roles'], function(key, value) {
                optgrouproles += '<option value="' + key + '">' + value + '</option>';
            });

            optgrouproles += "</optgroup>";

            $.each(data['locations'][local]['departments'], function(key, value) {
                optgroupdept += '<option value="dept-' + key + '">' + value + '</option>';
            });

            optgroupdept += "</optgroup>";

            $('#employee_roles').append(optgrouproles).append(optgroupdept);
            $.AdminBSB.select.refresh();
        });
    }
})();