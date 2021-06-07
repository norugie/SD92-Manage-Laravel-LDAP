@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>USER INFORMATION: {{ $user->getFirstAttribute('displayname') }}</h4>      
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <center>
                                    <a href="/cms/employees/{{ $user->getFirstAttribute('samaccountname') }}/update" type="button" class="btn bg-green waves-effect" style="display: inline-block;"><i class="material-icons">edit</i><span>UPDATE EMPLOYEE INFO</span></a>
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