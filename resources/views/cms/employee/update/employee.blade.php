@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-xs-sm-center">
                            <h4>USER INFORMATION: {{ $employee->getFirstAttribute('displayname') }}</h4>      
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <center>
                                <a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/view" type="button" class="btn bg-blue waves-effect" style="display: inline-block;"><i class="material-icons">person</i><span>VIEW EMPLOYEE</span></a>
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
                            <form class="new_form_validate" action="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/update" method="POST">
                                @csrf
                                <div class="row clearfix">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label for="employee_firstname">First Name *</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="employee_firstname" name="employee_firstname" value="{{ $employee->getFirstAttribute('givenname') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label for="employee_lastname">Last Name *</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="employee_lastname" name="employee_lastname" value="{{ $employee->getFirstAttribute('sn') }}" required>
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
                                                @foreach($config['locations'] as $key => $value)
                                                    <option value="{{ $key }}" @if($key === $employee->getFirstAttribute('department')) selected @endif>{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label for="employee_locations">Locations</label>
                                        <div class="form-group">
                                            <select class="form-control show-tick" multiple name="employee_locations[]" id="employee_locations" title="Select employee locations">
                                                {{-- Location Options --}}
                                                @foreach($config['locations'] as $key => $value)
                                                    <option value="{{ $key }}" @if(in_array($key, $locations)) selected @endif>{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label for="employee_roles">Roles and Sub-Departments</label>
                                        <div class="form-group">
                                            <select class="form-control show-tick" multiple name="employee_roles[]" id="employee_roles" title="Select employee roles">
                                                {{-- Role Options --}}
                                                <optgroup label="General Roles">
                                                    @foreach($config['global_roles'] as $key => $value)
                                                        <option value="{{ $key }}" @if(in_array($key, $sub_departments)) selected @endif>{{ $value }}</option>
                                                    @endforeach
                                                </optgroup>
                                                @if($employee->getFirstAttribute('department') !== NULL)
                                                    <optgroup label="{{ $employee->getFirstAttribute('department') }} Specific Roles">
                                                        @foreach($config['locations'][$employee->getFirstAttribute('department')]['local_roles'] as $key => $value)
                                                            <option value="{{ $key }}" @if(in_array($key, $sub_departments)) selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="{{ $employee->getFirstAttribute('department') }} Sub-Departments">
                                                        @foreach($config['locations'][$employee->getFirstAttribute('department')]['departments'] as $key => $value)
                                                            <option value="dept-{{ $key }}" @if(in_array($key, $sub_departments)) selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row clearfix">

                                </div>
                                <div class="row clearfix">
                                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="float: right;">
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

@section( 'custom-js' )

    <script src="/cms/js/emp-form.js"></script>

@endsection