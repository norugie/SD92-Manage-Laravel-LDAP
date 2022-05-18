@extends ( 'cms.layout.layout' )

@section( 'custom-css' )

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/cms/css/card.css" rel="stylesheet" />
    <link href="/cms/plugins/cropper/cropper.min.css" rel="stylesheet" />
    
@endsection

@section('custom-js')

    <script src="/cms/plugins/cropper/cropper.min.js"></script>
    <script src="/cms/js/cropper-card.js"></script>
    <script>
        $('#student_rfid_toggle').click(function() {
            $('#student_rfid_area').removeAttr('hidden');
            $('#student_rfid_area').show();
            $('#student_rfid_toggle').hide();
        });

        $('#student_rfid_cancel').click(function() {
            $('#student_rfid_area').hide();
            $('#student_rfid_toggle').show();
        });
    </script>

@endsection

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-xs-12 col-sm-4 col-md-3">
            <div class="card profile-card">
                <div class="profile-header">&nbsp;</div>
                <div class="profile-body">
                    <div class="image-area">
                        <img id="student_profile_img" src="{{ $student->getFirstAttribute('studentpic') }}" alt="Student ID Card Image" />
                    </div>
                    <div class="content-area">
                        <h3>{{ $student->getFirstAttribute('fullname') }}</h3>
                        <p>@if($student->getFirstAttribute('school') !== NULL) {{ $config['locations'][$student->getFirstAttribute('school')]['name'] }} @endif</p>
                    </div>
                </div>
                <div class="profile-footer">
                    <button type="button" id="student_rfid_toggle" class="btn bg-green waves-effect btn-block"><i class="material-icons">edit</i><span>ASSIGN STUDENT ID</span></button>
                    <div id="student_rfid_area" class="row" hidden>
                        <div class="col-lg-12">
                            <form action="/cms/students/{{ $student->getFirstAttribute('samaccountname') }}/update/{{ $student->getFirstAttribute('sysid') }}" method="POST">
                                @csrf
                                <div class="row" >
                                    <div class="col-md-12">
                                        <label for="student_rfid">Student ID Card Code</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" id="student_rfid" name="student_rfid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <button id="student_rfid_cancel" type="reset" class="btn btn-block btn-lg waves-effect">CANCEL</button> 
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <button id="student_rfid_save" type="submit" class="btn bg-blue-grey btn-block btn-lg waves-effect">SAVE</button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
                                <p><b>System User ID: </b>{{ $student->getFirstAttribute('sysid') }}</p>
                                <p><b>Student ID Card Code: </b>{{ $student->getFirstAttribute('studentNumber') }}</p>
                                <p><b>Student Name: </b>{{ $student->getFirstAttribute('fullname') }}</p>
                                <p><b>District Email Address: </b>{{ $student->getFirstAttribute('mail') }}</p>
                                <p><b>District Username: </b>{{ $student->getFirstAttribute('samaccountname') }}</p>
                                <p><b>School: </b>@if($student->getFirstAttribute('school') !== NULL) {{ $config['locations'][$student->getFirstAttribute('school')]['name'] }} @endif</p>
                                <p><b>Grade: </b>{{ $student->getFirstAttribute('grade') }}</p>
                            </div>
                            <div class="tab-pane fade" id="nav-id" role="tabpanel" aria-labelledby="nav-id-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-12"><i><b>Note:</b> The displayed card is only a rough preview. The final card may have slightly different layout.</i></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="new_card_img">
                                            <button type="button" id="upload_id_image" class="btn bg-green waves-effect" style="display: inline-block;" data-username="{{ $student->getFirstAttribute('samaccountname') }}" data-type="students" data-usernumber="{{ $student->getFirstAttribute('sysid') }}" onclick="$('#new_card_img').trigger('click'); return false;"><i class="material-icons">image</i><span>UPDATE ID IMAGE</span></button>
                                        </label>
                                        <input type="file" id="new_card_img" name="new_card_img" accept="image/*" style="display: none;">
                                        <label for="download_id_card">
                                            <a href="/cms/students/{{ $student->getFirstAttribute('samaccountname') }}/download/image" id="download_id_card" type="button" class="btn bg-blue waves-effect" style="display: inline-block;" download><i class="material-icons">file_download</i><span>DOWNLOAD ID</span></a>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="profile_card" class="card-id">
                                            <div class="card-top">
                                                <div class="card-title" style="margin-left:87px;">STUDENT</div>
                                                <div class="card-img">
                                                    <img id="student_card_img" src="{{ $student->getFirstAttribute('studentpic') }}" alt="student ID Card Image">
                                                </div>
                                                <div class="card-top-display">
                                                    <div class="card-logo">
                                                        <img src="/nisgaa-icon.png" alt="" width="57" height="90">
                                                    </div>
                                                    <div class="card-department">
                                                        {{ $config['locations'][$student->getFirstAttribute('school')]['name'] }} <br>
                                                        School District No. 92 (Nisga'a)<br>

                                                        @if( $config['locations'][$student->getFirstAttribute('school')]['address'] !== "" )
                                                        {!! 
                                                            $config['locations'][$student->getFirstAttribute('school')]['address'] . "<br>" .
                                                            $config['locations'][$student->getFirstAttribute('school')]['city'] . " " . 
                                                            $config['locations'][$student->getFirstAttribute('school')]['province'] . " " . 
                                                            $config['locations'][$student->getFirstAttribute('school')]['postal_code'] . "<br>"
                                                        !!}
                                                        @endif
                                                        {{ $config['locations'][$student->getFirstAttribute('school')]['phone'] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-mid">
                                                <div class="card-mid-text">
                                                    <br>
                                                    {{ $student->getFirstAttribute('fullname') }}
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