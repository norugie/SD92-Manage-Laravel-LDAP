@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>EMPLOYEE LIST</h4>      
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
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
                                    <th>School/Department</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>School/Department</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->getFirstAttribute('displayname') }}</td>
                                    <td>{{ $employee->getFirstAttribute('samaccountname') }}</td>
                                    <td>{{ $employee->getFirstAttribute('mail') }}</td>
                                    <td>{{ $employee->getFirstAttribute('department') }}</td>
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