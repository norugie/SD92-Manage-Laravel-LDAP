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
                        <img id="student_profile_img" src="{{ $student->getFirstAttribute('studentpic') }}" alt="Student ID Card Image" />
                    </div>
                    <div class="content-area">
                        <h3>{{ $student->getFirstAttribute('fullname') }}</h3>
                        <p>@if($student->getFirstAttribute('school') !== NULL) {{ $config['locations'][$student->getFirstAttribute('school')]['name'] }} @endif</p>
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
                                            <button type="button" id="upload_id_image" class="btn bg-green waves-effect" style="display: inline-block;" data-username="{{ $student->getFirstAttribute('samaccountname') }}" data-usernumber="{{ $student->getFirstAttribute('uidNumber') }}" onclick="$('#new_card_img').trigger('click'); return false;"><i class="material-icons">image</i><span>UPDATE ID IMAGE</span></button>
                                        </label>
                                        <input type="file" id="new_card_img" name="new_card_img" style="display: none;">
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