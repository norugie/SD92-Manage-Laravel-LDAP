@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>EMPLOYEE LIST</h4>      
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <center>
                                    <a href="/cms/employees/create" type="button" class="btn bg-blue waves-effect" style="display: inline-block;"><i class="material-icons">add</i><span>NEW EMPLOYEE</span></a>
                                </center>
                            </div>
                        </div>
                    </div>
                <div class="body">
                    <div class="table-responsive">
                        <table id="employee_table" class="table table-bordered table-striped table-hover dataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Main School/Department</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Main School/Department</th>
                                    <th>Options</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <center>
                                            <input type="checkbox" class="filled-in chk-col-blue-grey employee-checkbox" id="employee_checkbox_{{ $employee->getFirstAttribute('samaccountname') }}" name="employee_checkbox" value="{{ $employee->getFirstAttribute('samaccountname') }}" data-name="{{ $employee->getFirstAttribute('displayname') }}">
                                            <label for="employee_checkbox_{{ $employee->getFirstAttribute('samaccountname') }}"></label>
                                        </center>
                                    </td>
                                    <td><a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/view">{{ $employee->getFirstAttribute('displayname') }}</a></td>
                                    <td>{{ $employee->getFirstAttribute('samaccountname') }}</td>
                                    <td>{{ $employee->getFirstAttribute('mail') }}</td>
                                    <td>{{ $employee->getFirstAttribute('department') }}</td>
                                    <td>
                                        <center>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="material-icons">more_horiz</i><span>OPTIONS</span> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/view">View Employee</a></li>
                                                    <li><a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/update">Update Employee</a></li>
                                                    <li role="separator" class="divider"></li>
                                                    <li><a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/disable">Disable Employee</a></li>
                                                </ul>
                                            </div>
                                        </center>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="moveAccounts" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="moveAccountsLabel">Move the following user(s) to another department:</h4>
                </div>
                <form class="new_form_validate" action="/cms/employees/update" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="text" id="employee_multiple" name="employee_multiple" value="" hidden>
                        <input type="text" id="employee_multiple_name" name="employee_multiple_name" value="" hidden>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <ul id="employees-to-move"></ul>
                            </div>
                            <div class="col-lg-8 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="employee_department">Department/School *</label>
                                        <div class="form-group">
                                            <select class="form-control show-tick" name="employee_department" id="employee_department" title="Select employee department/school" required>
                                                {{-- Department Options --}}
                                                @foreach($config['locations'] as $key => $value)
                                                    <option value="{{ $key }}">{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="employee_locations">Locations</label>
                                        <div class="form-group">
                                            <select class="form-control show-tick" multiple name="employee_locations[]" id="employee_locations" title="Select employee locations">
                                                {{-- Location Options --}}
                                                @foreach($config['locations'] as $key => $value)
                                                    <option value="{{ $key }}">{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="employee_roles">Roles and Sub-Departments</label>
                                        <div class="form-group">
                                            <select class="form-control show-tick" multiple name="employee_roles[]" id="employee_roles" title="Select employee roles" data-live-search="true">
                                                {{-- Role Options --}}
                                                <optgroup label="General Roles">
                                                    @foreach($config['global_roles'] as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section( 'custom-js' )

    <script src="/cms/js/dt-buttons.js"></script>
    <script src="/cms/js/emp-form.js"></script>

@endsection