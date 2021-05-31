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
                                    <label for="employee_role">Role *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" name="employee_role" id="employee_role" title="Select employee role" required>
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
        setRole();

        // $('#employee_department').change(function(){
        //     setRole($(this).val());
        // });

        function setDepartment(){
            $.getJSON(departments, function( data ) {
                $.each(data['departments'], function(key, value) {
                    $('#employee_department').append('<option value="'+ key +'">' + value['name'] + '</option>');
                });
                $.AdminBSB.select.refresh();
            });
        }

        function setRole(){
            $.getJSON(departments, function( data ) {
                $.each(data['global'], function(key, value) {
                    $('#employee_role').append('<option value="'+ key +'">' + value + '</option>');
                });
                $.AdminBSB.select.refresh();
            });
        }
    })();
</script>

@endsection