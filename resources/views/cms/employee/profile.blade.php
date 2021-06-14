@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="card profile-card">
                <div class="profile-header">&nbsp;</div>
                <div class="profile-body">
                    <div class="image-area">
                        <img src="/cms/images/users/user-placeholder.png" alt="AdminBSB - Profile Image" />
                    </div>
                    <div class="content-area">
                        <h3>{{ $employee->getFirstAttribute('displayname') }}</h3>
                        <p>{{ $employee->getFirstAttribute('department') }}</p>
                    </div>
                </div>
                <div class="profile-footer">
                    <a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/update" type="button" class="btn bg-green waves-effect btn-block" style="display: inline-block;"><i class="material-icons">edit</i><span>UPDATE EMPLOYEE</span></a>
                    <a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/disable" type="button" class="btn bg-red waves-effect btn-block" style="display: inline-block;"><i class="material-icons">close</i><span>DISABLE EMPLOYEE</span></a>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-9">
            <div class="card">
                <div class="body">
                    <div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">User Info</a></li>
                            <li role="presentation"><a href="#id_card" aria-controls="settings" role="tab" data-toggle="tab">ID Card</a></li>
                            <li role="presentation"><a href="#access_settings" aria-controls="settings" role="tab" data-toggle="tab">Access Settings</a></li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="info">
                                {{-- User Info and Groups here --}}
                                <p><b>Employee Name: </b>{{ $employee->getFirstAttribute('displayname') }}</p>
                                <p><b>District Email Address: </b>{{ $employee->getFirstAttribute('mail') }}</p>
                                <p><b>District Username: </b>{{ $employee->getFirstAttribute('samaccountname') }}</p>
                                <p><b>Department/School: </b>{{ $employee->getFirstAttribute('department') }}</p>
                                <p><b>Sub-Department(s): </b></p>
                                <p><b>Location Access: </b></p>
                                <p><b>Miscellaneous Groups: </b></p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade in" id="id_card">
                                {{-- ID card settings --}}
                            </div>
                            <div role="tabpanel" class="tab-pane fade in" id="access_settings">
                                {{-- Access control settings --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection