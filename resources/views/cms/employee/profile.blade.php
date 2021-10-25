@extends ( 'cms.layout.layout' )

@section( 'custom-css' )

    <link href="/cms/css/card.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" integrity="sha512-0SPWAwpC/17yYyZ/4HSllgaK7/gg9OlVozq8K7rf3J8LvCjYEEIfzzpnA2/SSjpGIunCSD18r3UhvDcu/xncWA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection

@section('custom-js')
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" integrity="sha512-ooSWpxJsiXe6t4+PPjCgYmVfr1NS5QXJACcR/FPpsdm6kqG1FmQ2SVyg2RXeVuCRBLr0lWHnWJP6Zs1Efvxzww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/cms/js/cropper-card.js"></script>

@endsection

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="card profile-card">
                <div class="profile-header">&nbsp;</div>
                <div class="profile-body">
                    <div class="image-area">
                        <img src="{{ $employee_pic }}" alt="Employee ID Card Image" />
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
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">User Info</a></li>
                            <li role="presentation"><a href="#id_card" aria-controls="settings" role="tab" data-toggle="tab">ID Card</a></li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="info">
                                {{-- User Info and Groups here --}}
                                <p><b>Employee ID: </b>{{ $employee->getFirstAttribute('employeeID') }}</p>
                                <p><b>System User ID: </b>{{ $employee->getFirstAttribute('uid') }}</p>
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
                            <div role="tabpanel" class="tab-pane fade in" id="id_card">
                                {{-- ID card settings --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-12"><i><b>Note:</b> The displayed card is only a rough preview. The final card may have slightly different layout. Active image fetched from the School Management System cannot be edited.</i></p><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-id">
                                            <div class="card-top">
                                                <div class="card-title">EMPLOYEE</div>
                                                <div class="card-img">
                                                    <img id="employee-card-img" src="{{ $employee_pic }}" alt="Employee ID Card Image">
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
                                                            $config['locations'][$employee->getFirstAttribute('department')]['address'] . ",<br>" .
                                                            $config['locations'][$employee->getFirstAttribute('department')]['city'] . ", " . 
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

@endsection