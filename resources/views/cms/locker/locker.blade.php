@extends ( 'cms.layout.layout' )

@section ( 'content' )


    <div class="row clearfix">
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xs-sm-center">
                            <h4>Cart Locker #1</h4>      
                        </div>
                    </div>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered-locker">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Locker Number</th>
                                <th>Assigned student</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Status</th>
                                <th>Locker Number</th>
                                <th>Assigned student</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>
                                    <center><div style="background:green;width:16px;height:16px;border-radius:8px"></div></center>
                                </td>
                                <td>15</td>
                                <td>Emma Wilson</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection