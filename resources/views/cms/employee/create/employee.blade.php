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
                                            <option value="SDO">School District Board Office</option>
                                            <option value="TechOffice">IT Department</option>
                                            <option value="Maintenance">Maintenance Department</option>
                                            <option value="AAMES">AAMES</option>
                                            <option value="GES">GES</option>
                                            <option value="NESS">NESS</option>
                                            <option value="NBES">NBES</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label for="employee_role">Role *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" name="employee_role" id="employee_role" title="Select employee role" required>
                                            {{-- SDO Options --}}
                                            <option value="SDO">School District Board Office</option>
                                            {{-- TechOffice Options --}}
                                            {{-- Maintenance Options --}}
                                            {{-- AAMES Options --}}
                                            {{-- GES Options --}}
                                            {{-- NESS Options --}}
                                            {{-- NBES Options --}}
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

<script>

</script>

@endsection