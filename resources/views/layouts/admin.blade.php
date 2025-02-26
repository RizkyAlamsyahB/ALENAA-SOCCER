<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Zoter - Responsive Bootstrap 4 Admin Dashboard</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('assets/template/assets/images/logo-lg.png') }}">

    <!-- jvectormap -->
    <link href="{{ asset('assets/template/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/template/assets/plugins/fullcalendar/vanillaCalendar.css') }}" rel="stylesheet"
        type="text/css">

    <link href="{{ asset('assets/template/assets/plugins/morris/morris.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/template/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/template/assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/template/assets/css/style.css') }}" rel="stylesheet" type="text/css">


</head>


<body class="fixed-left">

    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- ========== Left Sidebar Start ========== -->
        @include('layouts.partials.sidebar')
        <!-- Left Sidebar End -->

        <!-- Start right Content here -->

        <div class="content-page">
            <!-- Start content -->
            <div class="content">

                <!-- Top Bar Start -->
                @include('layouts.partials.topbar')
                <!-- Top Bar End -->

                <div class="page-content-wrapper ">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="page-title-box">
                                    <div class="btn-group float-right">
                                        <ol class="breadcrumb hide-phone p-0 m-0">
                                            <li class="breadcrumb-item"><a href="#">Zoter</a></li>
                                            <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">@yield('title', 'Dashboard')</h4>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- end page title end breadcrumb -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="mt-0 header-title">@yield('header-title', 'Welcome to Zoter Dashboard!')</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @yield('content')
                    </div>
                    <!-- Page content Wrapper -->

                </div> <!-- content -->

                <footer class="footer">
                    © 2019 Zoter by Mannatthemes.
                </footer>

            </div>
            <!-- End Right content here -->

        </div>
        <!-- END wrapper -->

        <!-- jQuery  -->
        <script src="{{ asset('assets/template/assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/modernizr.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/detect.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/fastclick.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/waves.js') }}"></script>
        <script src="{{ asset('assets/template/assets/js/jquery.nicescroll.js') }}"></script>

        <script src="{{ asset('assets/template/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

        <script src="{{ asset('assets/template/assets/plugins/skycons/skycons.min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/plugins/fullcalendar/vanillaCalendar.js') }}"></script>

        <script src="{{ asset('assets/template/assets/plugins/raphael/raphael-min.js') }}"></script>
        <script src="{{ asset('assets/template/assets/plugins/morris/morris.min.js') }}"></script>

        <script src="{{ asset('assets/template/assets/pages/dashborad.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/template/assets/js/app.js') }}"></script>

</body>

</html>
