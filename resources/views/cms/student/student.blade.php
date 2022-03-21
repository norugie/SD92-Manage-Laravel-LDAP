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
                        <table id="student_table" class="table dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="all">Name</th>
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
                                    <td><a href="/cms/students/{{ $student->userid }}/view">{{ $student->fullname }}</a></td>
                                    <td>{{ $student->userid }}</td>
                                    <td>{{ $student->uid }}</td>
                                    <td>{{ $student->userid }}@nisgaa.bc.ca</td>
                                    <td>{{ $student->school }}</td>
                                    <td></td>
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