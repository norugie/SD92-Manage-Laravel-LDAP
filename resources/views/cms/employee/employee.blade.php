@extends ( 'cms.layout.layout' )

@section( 'custom-js' )

    <script src="/cms/js/mv-da-dt-buttons.js"></script>
    <script src="/cms/js/emp-form.js"></script>

@endsection

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
                                    <th>System User ID</th>
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
                                    <th>System User ID</th>
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
                                    <td>{{ $employee->getFirstAttribute('uidNumber') }}</td>
                                    <td>{{ $employee->getFirstAttribute('mail') }}</td>
                                    <td>{{ $employee->getFirstAttribute('department') }}</td>
                                    <td>
                                        <center>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="material-icons">more_horiz</i><span>OPTIONS</span> <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/view">View Employee</a>
                                                    <a class="dropdown-item" href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/update">Update Employee</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/disable">Disable Employee</a>
                                                </div>
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

    {{-- Modal - Move Accounts --}}
    @include( 'cms.layout.modals.move' )

    {{-- Modal - Disable Accounts --}}
    @include( 'cms.layout.modals.disable' )

@endsection