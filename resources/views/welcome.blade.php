<script src="/cms/plugins/jquery/jquery.min.js"></script>

<select name="employee_role" id="employee_role"></select>

<script>
    $.getJSON("/cms/groups/NESS.json", function( data ) {
        $.each(data, function(key, value) {
            $('#employee_role').append('<option value="'+ key +'">' + data[key] + '</option>');
            console.log(key + " " + data[key]);
        });
        // console.log(data);
    });
</script>