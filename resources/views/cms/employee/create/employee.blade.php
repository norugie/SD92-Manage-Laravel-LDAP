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
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="employee_department">Main Department/School *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" name="employee_department" id="employee_department" title="Select employee department/school" required>
                                            {{-- Department Options --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="employee_locations">Locations</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_locations[]" id="employee_locations" title="Select employee locations">
                                            {{-- Location Options --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="employee_roles">Roles and Sub-Departments</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_roles[]" id="employee_roles" title="Select employee roles">
                                            {{-- Role Options --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">

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

<script src="/cms/js/emp-form.js"></script>

@endsection