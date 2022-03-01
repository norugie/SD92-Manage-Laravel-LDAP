<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>Dasboard | SD92 - User Manager</title>
        <!-- Favicon-->
        <link rel="icon" href="/nisgaa-icon.png" type="image/x-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

        <!-- Bootstrap Core Css -->
        <link href="/cms/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

        <!-- Waves Effect Css -->
        <link href="/cms/plugins/node-waves/waves.css" rel="stylesheet" />

        <!-- Animation Css -->
        <link href="/cms/plugins/animate-css/animate.css" rel="stylesheet" />

        <!-- Bootstrap Select Css -->
        <link href="/cms/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

        <!-- JQuery DataTable Css -->
        <link href="/cms/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

        <!-- Custom Css -->
        <link href="/cms/css/style.css" rel="stylesheet">
        <link href="/cms/css/themes/theme-blue-grey.css" rel="stylesheet" />
        <link href="/cms/css/custom-backend.css" rel="stylesheet">
        @yield('custom-css')

        <!-- Jquery Core Js -->
        <script src="/cms/plugins/jquery/jquery.min.js"></script>

        <!-- Jquery Validation Plugin Css -->
        <script src="/cms/plugins/jquery-validation/jquery.validate.js"></script>
    </head>
    <body class="theme-blue-grey">
        <!-- CMS Content -->

        <!-- Page Loader -->
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="preloader">
                    <div class="spinner-layer pl-blue-grey">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Please wait...</p>
            </div>
        </div>
        <!-- END - Page Loader -->

        <!-- Page Sidebar Overlay -->
        <div class="overlay"></div>
        <!-- END - Page Sidebar Overlay -->

        <!-- Page Navbar -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                    <a href="javascript:void(0);" class="bars"></a>
                    <a class="navbar-brand" href="/">SD92 - NISGA'A USER MANAGER</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Logout -->
                        <li><a href="/signout"><i class="material-icons">input</i></a></li>
                        <!-- END - Logout -->
                    </ul>
                </div>
            </div>
        </nav>
        <!-- END - Page Navbar -->

        <!-- Page Sidebar -->
        <section>
            <aside id="leftsidebar" class="sidebar">
                <!-- User Info -->
                <div class="user-info">
                    <div class="info-container">
                        <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $data->userName }}</div>
                        <div class="email">{{ $data->userEmail }}</div>
                    </div>
                </div>
                <!-- #User Info -->
                <!-- Menu -->
                <div class="menu">
                    <ul class="list">
                        <li class="header">GENERAL</li>
                        <li id="dashboard" class="{{ Request::is( 'cms/dashboard' ) ? 'active' : '' }}">
                            <a href="/cms/dashboard">
                                <i class="material-icons">dashboard</i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="header">EMPLOYEES</li>
                        <li id="employees" class="{{ Request::is( 'cms/employees' ) || Request::is( 'cms/employees/*' ) ? 'active' : '' }}">
                            <a href="/cms/employees">
                                <i class="material-icons">people</i>
                                <span>Active Employee List</span>
                            </a>
                        </li>
                        <li id="inactive-employees" class="{{ Request::is( 'cms/inactive' ) || Request::is( 'cms/inactive/*' ) ? 'active' : '' }}">
                            <a href="/cms/inactive">
                                <i class="material-icons">lock</i>
                                <span>Inactive Employee List</span>
                            </a>
                        </li>
                        <li class="header">STUDENTS</li>
                        <li id="students" class="{{ Request::is( 'cms/students' ) || Request::is( 'cms/students/*' ) ? 'active' : '' }}">
                            <a href="/cms/students">
                                <i class="material-icons">people</i>
                                <span>Active Student List</span>
                            </a>
                        </li>
                        <li id="lockers" class="{{ Request::is( 'cms/lockers' ) || Request::is( 'cms/lockers/*' ) ? 'active' : '' }}">
                            <a href="/cms/lockers">
                                <i class="material-icons">domain</i>
                                <span>Student Locker Status</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- #Menu -->
                <!-- Footer -->
                <div class="legal">
                    <div class="version">
                        &copy; <b>2021. SD92 - User Manager</b> v1.0
                    </div>
                    <div class="copyright">
                        Design Template by <b><a href="https://github.com/gurayyarar/AdminBSBMaterialDesign">Güray Yarar</a></b>
                    </div>
                </div>
                <!-- #Footer -->
            </aside>
        </section>
        <!-- END - Page Sidebar -->

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                @include( 'cms.layout.notification' )
                <div class="block-header">
                    <h2>{{ strtoupper($data->pageSection) }} @isset($data->pageSubSection) {{ " - " . strtoupper($data->pageSubSection) }} @endisset</h2>
                </div>

                <!-- Page Content -->
                @yield ( 'content' )
                <!-- END Page Content -->

            </div>
        </section>
        <!-- END - Main Content -->

        <!-- Page Footer -->
        <!-- Bootstrap Core Js -->
        <script src="/cms/plugins/bootstrap/js/bootstrap.js"></script>

        <!-- Slimscroll Plugin Js -->
        <script src="/cms/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

        <!-- Waves Effect Plugin Js -->
        <script src="/cms/plugins/node-waves/waves.js"></script>

        <!-- Jquery DataTable Plugin Js -->
        <script src="/cms/plugins/jquery-datatable/jquery.dataTables.js"></script>
        <script src="/cms/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
        <script src="/cms/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

        <!-- Select Plugin Js -->
        <script src="/cms/plugins/bootstrap-select/js/bootstrap-select.js"></script>

        <!-- Validation Plugin Js -->
        <script src="/cms/plugins/jquery-validation/jquery.validate.js"></script>

        <!-- Custom Js -->
        <script src="/cms/js/admin.js"></script>
        <script src="/cms/js/custom.js"></script>
        @yield( 'custom-js' )
        <!-- END - Page Footer -->
    </body>
</html>