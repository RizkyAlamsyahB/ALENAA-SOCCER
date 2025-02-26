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
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- end page title end breadcrumb -->
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex flex-row">
                                                    <div class="col-3 align-self-center">
                                                        <div class="round">
                                                            <i class="mdi mdi-eye"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-9 align-self-center text-right">
                                                        <div class="m-l-10">
                                                            <h5 class="mt-0">18090</h5>
                                                            <p class="mb-0 text-muted">Visits Today <span
                                                                    class="badge bg-soft-success"><i
                                                                        class="mdi mdi-arrow-up"></i>2.35%</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-3" style="height:3px;">
                                                    <div class="progress-bar  bg-success" role="progressbar"
                                                        style="width: 35%;" aria-valuenow="35" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->

                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex flex-row">
                                                    <div class="col-3 align-self-center">
                                                        <div class="round">
                                                            <i class="mdi mdi-account-multiple-plus"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-9 text-right align-self-center">
                                                        <div class="m-l-10 ">
                                                            <h5 class="mt-0">562</h5>
                                                            <p class="mb-0 text-muted">New Users</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-3" style="height:3px;">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: 48%;" aria-valuenow="48" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->

                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="search-type-arrow"></div>
                                                <div class="d-flex flex-row">
                                                    <div class="col-3 align-self-center">
                                                        <div class="round ">
                                                            <i class="mdi mdi-cart"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-9 align-self-center text-right">
                                                        <div class="m-l-10 ">
                                                            <h5 class="mt-0">7514</h5>
                                                            <p class="mb-0 text-muted">New Orders</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-3" style="height:3px;">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 61%;" aria-valuenow="61" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->
                                </div><!--end row-->

                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="mt-0 header-title">Every Day Revenue</h4>
                                        <p class="text-muted mb-4 font-14"></p>
                                        <div id="morris-bar-stacked" class="morris-chart"></div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->

                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h3> $ 40214.00</h3>
                                                <h6 class="text-lightdark">Total Sele</h6>
                                                <span class="text-muted"> <small>Last 6 Month</small></span>
                                            </div>
                                            <div class="col-4 text-center">
                                                <h5><i class="mdi mdi-airplane-takeoff mr-2 text-danger font-20"></i>
                                                    80%</h5>
                                                <h6 class="text-lightdark">Export</h6>
                                                <span class="text-muted"> <small>2018 to 2019</small></span>
                                            </div>
                                        </div>
                                    </div><!--end card-body-->
                                    <div class="card-body p-0 mb-n5">
                                        <div class="mb-0 area-chart-map" id="morris-area-chart"></div>
                                    </div>
                                    <div class="card mb-0 bg-map">
                                        <div class="card-body ">
                                            <div id="world-map-markers" class="dash-map"></div>
                                        </div>
                                    </div><!--end card-->
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->

                        @yield('content')
                    </div><!-- container -->

                </div> <!-- Page content Wrapper -->

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
