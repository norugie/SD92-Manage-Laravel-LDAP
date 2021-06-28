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
                        <table id="employee-table" class="table table-bordered table-striped table-hover dataTable">
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
                                            <input type="checkbox" class="filled-in chk-col-blue-grey employee-checkbox" id="employee_checkbox_{{ $employee->getFirstAttribute('samaccountname') }}" name="employee_checkbox" value="{{ $employee->getFirstAttribute('samaccountname') }}">
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

    <form action="">
        @csrf
        <input type="text" id="employee_multiple" name="employee_multiple" value="">
    </form>

    <div class="modal fade" id="moveAccounts" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="moveAccountsLabel">Move the following user(s) to another department:</h4>
                </div>
                <div class="modal-body">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sodales orci ante, sed ornare eros vestibulum ut. Ut accumsan
                    vitae eros sit amet tristique. Nullam scelerisque nunc enim, non dignissim nibh faucibus ullamcorper.
                    Fusce pulvinar libero vel ligula iaculis ullamcorper. Integer dapibus, mi ac tempor varius, purus
                    nibh mattis erat, vitae porta nunc nisi non tellus. Vivamus mollis ante non massa egestas fringilla.
                    Vestibulum egestas consectetur nunc at ultricies. Morbi quis consectetur nunc.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link waves-effect">SAVE CHANGES</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section( 'custom-js' )

    <script>
        (function() {
            $('.move-accounts')
                .attr('disabled', true)
                .attr('data-toggle', 'modal')
                .attr('data-target', '#moveAccounts'); 
            $('.disable-accounts').attr('disabled', true);

            $('.employee-checkbox').change(function() {
                var employee = '#employee_checkbox_' + $(this).val();

                var employeeName = $(employee).val();
                var employeeNameList = $('#employee_multiple').val();
                if(employeeNameList.includes(employeeName + ',') ? employeeNameList = employeeNameList.replace(employeeName + ',', '') : employeeNameList = employeeNameList + employeeName + ',');
                    
                $('#employee_multiple').attr('value', employeeNameList);

                if($('#employee_multiple').val().length === 0 ? $('.move-accounts').attr('disabled', true) : $('.move-accounts').removeAttr('disabled'));
            });
        })();
    </script>

@endsection