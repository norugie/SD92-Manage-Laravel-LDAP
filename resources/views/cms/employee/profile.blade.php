@extends ( 'cms.layout.layout' )

@section( 'custom-css' )

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/cms/css/card.css" rel="stylesheet" />
    <link href="/cms/plugins/cropper/cropper.min.css" rel="stylesheet" />
    
@endsection

@section('custom-js')

    <script src="/cms/plugins/cropper/cropper.min.js"></script>
    <script src="/cms/js/cropper-card.js"></script>

@endsection

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="card profile-card">
                <div class="profile-header">&nbsp;</div>
                <div class="profile-body">
                    <div class="image-area">
                        <img id="employee_profile_img" src="{{ $employee_pic }}" alt="Employee ID Card Image" />
                    </div>
                    <div class="content-area">
                        <h3>{{ $employee->getFirstAttribute('displayname') }}</h3>
                        <p>@if($employee->getFirstAttribute('department') !== NULL) {{ $config['locations'][$employee->getFirstAttribute('department')]['name'] }} @endif</p>
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
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-user-tab" data-toggle="tab" href="#nav-user" role="tab" aria-controls="nav-user" aria-selected="true">User Info</a>
                                <a class="nav-item nav-link" id="nav-id-tab" data-toggle="tab" href="#nav-id" role="tab" aria-controls="nav-id" aria-selected="false">ID Card</a>
                            </div>
                        </nav>
                        <br>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-user" role="tabpanel" aria-labelledby="nav-user-tab">
                                {{-- User Info and Groups here --}}
                                <p><b>Employee ID: </b>{{ $employee->getFirstAttribute('employeeID') }}</p>
                                <p><b>System User ID: </b>{{ $employee->getFirstAttribute('uidNumber') }}</p>
                                <p><b>Employee ID Card Code: </b>{{ $employee->getFirstAttribute('employeeNumber') }}</p>
                                <p><b>Employee Name: </b>{{ $employee->getFirstAttribute('displayname') }}</p>
                                <p><b>District Email Address: </b>{{ $employee->getFirstAttribute('mail') }}</p>
                                <p><b>District Username: </b>{{ $employee->getFirstAttribute('samaccountname') }}</p>
                                <p><b>Department/School: </b>@if($employee->getFirstAttribute('department') !== NULL) {{ $config['locations'][$employee->getFirstAttribute('department')]['name'] }} @endif</p>
                                {{-- Note: Code below could be shorter if decided to use actual names of AD groups instead of user-friendly names --}}
                                <p><b>Sub-Department(s): </b>
                                    @if($employee->getFirstAttribute('department') !== NULL)
                                        @foreach($config['locations'][$employee->getFirstAttribute('department')]['departments'] as $key => $value)
                                            @if(in_array($key, $sub_departments)) {{ $value }},  @endif
                                        @endforeach
                                    @endif
                                </p>
                                <p><b>Location Access: </b>
                                    @foreach($config['locations'] as $key => $value)
                                        @if(in_array($key, $locations)) {{ $value['name'] }},  @endif
                                    @endforeach
                                </p>
                                <p><b>Roles and Miscellaneous Groups: </b>
                                    @foreach($config['global_roles'] as $key => $value)
                                        @if(in_array($key, $sub_departments)) {{ $value }},  @endif
                                    @endforeach
                                    @if($employee->getFirstAttribute('department') !== NULL)
                                        @foreach($config['locations'][$employee->getFirstAttribute('department')]['local_roles'] as $key => $value)
                                            @if(in_array($key, $sub_departments)) {{ $value }},  @endif
                                        @endforeach
                                    @endif
                                </p>
                            </div>
                            <div class="tab-pane fade" id="nav-id" role="tabpanel" aria-labelledby="nav-id-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-12"><i><b>Note:</b> The displayed card is only a rough preview. The final card may have slightly different layout.</i></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="new_card_img">
                                            <button type="button" id="upload_id_image" class="btn bg-green waves-effect" style="display: inline-block;" data-username="{{ $employee->getFirstAttribute('samaccountname') }}" data-usernumber="{{ $employee->getFirstAttribute('uidNumber') }}" onclick="$('#new_card_img').trigger('click'); return false;"><i class="material-icons">image</i><span>UPDATE ID IMAGE</span></button>
                                        </label>
                                        <input type="file" id="new_card_img" name="new_card_img" style="display: none;">
                                        <label for="download_id_card">
                                            <a href="/cms/employees/{{ $employee->getFirstAttribute('samaccountname') }}/download/image" id="download_id_card" type="button" class="btn bg-blue waves-effect" style="display: inline-block;" download><i class="material-icons">file_download</i><span>DOWNLOAD ID</span></a>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="profile_card" class="card-id">
                                            <div class="card-top">
                                                <div class="card-title">EMPLOYEE</div>
                                                <div class="card-img">
                                                    <img id="employee_card_img" src="{{ $employee_pic }}" alt="Employee ID Card Image">
                                                </div>
                                                <div class="card-top-display">
                                                    <div class="card-logo">
                                                        <img src="/nisgaa-icon.png" alt="" width="57" height="90">
                                                    </div>
                                                    <div class="card-department">
                                                        {{ $config['locations'][$employee->getFirstAttribute('department')]['name'] }} <br>
                                                        School District No. 92 (Nisga'a)<br>

                                                        @if( $config['locations'][$employee->getFirstAttribute('department')]['address'] !== "" )
                                                        {!! 
                                                            $config['locations'][$employee->getFirstAttribute('department')]['address'] . "<br>" .
                                                            $config['locations'][$employee->getFirstAttribute('department')]['city'] . " " . 
                                                            $config['locations'][$employee->getFirstAttribute('department')]['province'] . " " . 
                                                            $config['locations'][$employee->getFirstAttribute('department')]['postal_code'] . "<br>"
                                                        !!}
                                                        @endif
                                                        {{ $config['locations'][$employee->getFirstAttribute('department')]['phone'] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-mid">
                                                <div class="card-mid-text">
                                                    <br>
                                                    {{ $employee->getFirstAttribute('displayname') }}
                                                    <br>
                                                    <img src="/cms/images/barcode.png" alt="" height="50">
                                                </div>
                                            </div>
                                            <div class="card-bottom"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal - Image Cropper --}}
    @include( 'cms.layout.modals.image' )

@endsection