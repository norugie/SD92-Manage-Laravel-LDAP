@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>STUDENT LIST</h4>      
                            </div>
                        </div>
                    </div>
                <div class="body">
                    <div class="table-responsive">
                        <table id="student_table" class="table table-bordered table-striped table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>System User ID</th>
                                    <th>Email Address</th>
                                    <th>School</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>System User ID</th>
                                    <th>Email Address</th>
                                    <th>School</th>
                                    <th>Grade</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td><a href="/cms/students/{{ $student->getFirstAttribute('samaccountname') }}/view">{{ $student->getFirstAttribute('fullname') }}</a></td>
                                    <td>{{ $student->getFirstAttribute('samaccountname') }}</td>
                                    <td>{{ $student->getFirstAttribute('sysid') }}</td>
                                    <td>{{ $student->getFirstAttribute('mail') }}</td>
                                    <td>{{ $student->getFirstAttribute('school') }}</td>
                                    <td>{{ $student->getFirstAttribute('grade') }}</td>
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