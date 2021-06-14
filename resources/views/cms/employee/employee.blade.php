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
                        <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Main School/Department</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
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

@endsection