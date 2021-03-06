@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h5>LOGS</h5>
                                <p class="text-small"><i>Note: The log table below shows the log entries from the past 30 days only.</i></p>
                            </div>
                        </div>
                    </div>
                <div class="body">
                    The current Laravel version is {{ app()->version() }}
                    <div class="table-responsive">
                        <table id="log_table" class="table dt-responsive" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Log User</th>
                                    <th class="all">Log Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Log User</th>
                                    <th>Log Description</th>
                                    <th>Date</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->log_user }}</td>
                                    <td>{!! $log->log_description !!}</td>
                                    <td>{{ $log->created_at->format( 'd M Y - g:i:s a' ) }}</td>
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