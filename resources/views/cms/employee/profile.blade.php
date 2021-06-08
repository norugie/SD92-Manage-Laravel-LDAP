@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-8 col-md8 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>USER INFORMATION: {{ $user->getFirstAttribute('displayname') }}</h4>      
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <center>
                                    <a href="/cms/employees/{{ $user->getFirstAttribute('samaccountname') }}/update" type="button" class="btn bg-green waves-effect" style="display: inline-block;"><i class="material-icons">edit</i><span>UPDATE EMPLOYEE INFO</span></a>
                                    <a href="/cms/employees" type="button" class="btn bg-blue waves-effect" style="display: inline-block;"><i class="material-icons">list</i><span>EMPLOYEE LIST</span></a>
                                </center>
                            </div>
                        </div>
                    </div>
                <div class="body">

                </div>
            </div>
        </div>
    </div>

@endsection