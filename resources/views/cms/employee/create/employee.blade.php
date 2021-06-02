@extends ( 'cms.layout.layout' )

@section ( 'content' )

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <div class="row clearfix">
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 text-xs-sm-center">
                        <h4>NEW EMPLOYEE</h4>      
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <center>
                            <a href="/cms/employees" type="button" class="btn bg-blue waves-effect" style="display: inline-block;"><i class="material-icons">list</i><span>EMPLOYEE LIST</span></a>
                        </center>
                    </div>
                </div>
            </div>
            <div class="body">
                <p class="font-12"><i><b>Note:</b> Fields marked with an asterisk are required</i></p><br>
                <!-- Inline Layout -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <form class="new_form_validate" action="/cms/employees/create" method="POST">
                            @csrf
                            <div class="row clearfix">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label for="employee_firstname">First Name *</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="employee_firstname" name="employee_firstname" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label for="employee_lastname">Last Name *</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="employee_lastname" name="employee_lastname" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label for="employee_department">Department/School *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" name="employee_department" id="employee_department" title="Select employee department" required>
                                            {{-- Department Options --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label for="employee_locations">Locations *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_locations[]" id="employee_locations" title="Select employee locations" required>
                                            {{-- Location Options --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label for="employee_roles">Roles *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_roles[]" id="employee_roles" title="Select employee roles" required>
                                            {{-- Role Options --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="float: right; margin-right: 12px;">
                                    <button type="submit" class="btn bg-blue-grey btn-block btn-lg waves-effect">SAVE</button>  
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- #END# Inline Layout -->
            </div>
        </div>
    </div>
</div>

@endsection

@section( 'custom' )

<script>
    (function () {
        var departments = "/cms/departments/departments.json";

        setDepartment();
        setLocations();
        setRoles();

        $('#employee_locations').change(function(){
            $('#employee_roles').empty();
            var local = $(this).val();

            setRoles();

            $.each(local, function(key, value){
                var optgroup = '<optgroup label="'+ value +'">';
                
                $.getJSON(departments, function(data) {
                    $.each(data['departments'][value]['local'], function(key, value) {
                        optgroup += '<option value="'+ key +'">'+ value +'</option>';
                    });

                    optgroup += "</optgroup>";

                    $('#employee_roles').append(optgroup);
                    $.AdminBSB.select.refresh();
                });
            });
        });

        function setDepartment(){
            $.getJSON(departments, function(data) {
                $.each(data['departments'], function(key, value) {
                    $('#employee_department').append('<option value="'+ key +'">' + value['name'] + '</option>');
                });
                $.AdminBSB.select.refresh();
            });
        }

        function setLocations(){
            $.getJSON(departments, function(data) {
                $.each(data['departments'], function(key, value) {
                    $('#employee_locations').append('<option value="'+ key +'">' + value['name'] + '</option>');
                });
                $.AdminBSB.select.refresh();
            });
        }

        function setRoles(){
            var optgroup = '<optgroup label="General Roles">';
            $.getJSON(departments, function(data) {
                $.each(data['global'], function(key, value) {
                    optgroup += '<option value="'+ key +'">' + value + '</option>';
                });
                optgroup += "</optgroup>";
                $('#employee_roles').append(optgroup);
                $.AdminBSB.select.refresh();
            });
        }
    })();
</script>

@endsection