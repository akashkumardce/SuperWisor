<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/plugins/images/favicon.png') }}">
    <title>Super Wisor - @yield('title')</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="{{ asset('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    <!-- toast CSS -->
    <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    <!-- morris CSS -->
    <link href="{{ asset('assets/plugins/bower_components/morrisjs/morris.css') }}" rel="stylesheet">
    <!-- chartist CSS -->
    <link href="{{ asset('assets/plugins/bower_components/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}"
          rel="stylesheet">
    <!-- animation CSS -->
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{ asset('assets/css/colors/default.css') }}" id="theme" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fix-header">

<!-- ============================================================== -->
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>
</div>

<div id="wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header">
            <div class="top-left-part">
                <!-- Logo -->
                <a class="logo" href="dashboard.html">
                    <!-- Logo icon image, you can use font-icon also -->
                    <b>
                        <!--This is dark logo icon-->
                        <img src="{{ asset('assets/plugins/images/admin-logo.png') }}" alt="home" class="dark-logo"/>
                        <!--This is light logo icon-->
                        <img src="{{ asset('assets/plugins/images/admin-logo-dark.png') }}" alt="home"
                             class="light-logo"/>
                    </b>
                    <!-- Logo text image you can use text also -->
                    <span style="color: #2b1b1b;">
                            <!--This is dark logo text-->
                            Super Wisor
                        </span>
                </a>
            </div>
            <!-- /Logo -->
            <ul class="nav navbar-top-links navbar-right pull-right">
                <li>
                    <a class="nav-toggler open-close waves-effect waves-light hidden-md hidden-lg"
                       href="javascript:void(0)"><i class="fa fa-bars"></i></a>
                </li>
                <li>
                    <a class="profile-pic" href="#"> <img src="{{ asset('assets/plugins/images/users/user.jpg') }}"
                                                          alt="user-img" width="36" class="img-circle"><b
                                class="hidden-xs">{{ Auth::user()->name }}</b></a>
                </li>
                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- End Top Navigation -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav slimscrollsidebar">
            <div class="sidebar-head">
                <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span>
                </h3>
            </div>
            <ul class="nav" id="side-menu">
                <li style="padding: 70px 0 0;">
                    <a href="{{ url('dashboard')  }}" class="waves-effect"><i class="fa fa-clock-o fa-fw"
                                                                              aria-hidden="true"></i>Dashboard</a>
                </li>
                @if(Auth::user()->role == 'ADMIN')
                    <li>
                        <a href="{{ url('team-manage')  }}" class="waves-effect"><i class="fa fa-users fa-fw"
                                                                                    aria-hidden="true"></i>Team
                            Administration</a>
                    </li>
                    <li>
                        <a href="{{ url('user-manage')  }}" class="waves-effect"><i class="fa fa-user fa-fw"
                                                                                    aria-hidden="true"></i>User
                            Administration</a>
                    </li>
                    <li>
                        <a href="{{ url('server-manage')  }}" class="waves-effect"><i class="fa fa-server fa-fw"
                                                                                      aria-hidden="true"></i>Server
                            Administration</a>
                    </li>
                @else
                    <li>
                        <a href="{{ url('team-manage')  }}" class="waves-effect"><i class="fa fa-users fa-fw"
                                                                                    aria-hidden="true"></i>Team
                            View</a>
                    </li>
                @endif
                <li>
                    <a href="{{ url('service-manage')  }}" class="waves-effect"><i class="fa fa-cog fa-fw"
                                                                                   aria-hidden="true"></i>Service
                        Administration</a>
                </li>
            </ul>
        </div>

    </div>
    <!-- ============================================================== -->
    <!-- End Left Sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page Content -->
    <!-- ============================================================== -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                    <h4 class="page-title">@yield('title')</h4></div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    <ol class="breadcrumb">
                        <li><a href="#">@yield('title')</a></li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>

            @yield('content')
        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2020 &copy; OpenSource by Akash Kumar</footer>
    </div>
    <!-- ============================================================== -->
    <!-- End Page Content -->
    <!-- ============================================================== -->
</div>

<!-- ============================================================== -->
<script src="{{ asset('assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<!-- Menu Plugin JavaScript -->
<script src="{{ asset('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--slimscroll JavaScript -->
<script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('assets/js/waves.js') }}"></script>
<!--Counter js -->
<script src="{{ asset('assets/plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('assets/plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
<!-- chartist chart -->
<script src="{{ asset('assets/plugins/bower_components/chartist-js/dist/chartist.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>
<!-- Sparkline chart JavaScript -->
<script src="{{ asset('assets/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('assets/js/custom.min.js') }}"></script>
<script src="{{ asset('assets/js/dashboard1.js') }}"></script>
<script src="{{ asset('assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>


<script>
    function success(title, message) {
        $.toast({
            heading: title,
            text: message,
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: 'success',
            hideAfter: 5000,
            stack: 6
        });
    }

    function error(title, message) {
        $.toast({
            heading: title,
            text: message,
            position: 'top-right',
            loaderBg: '#ff6849',
            icon: 'error',
            hideAfter: 5000,
            stack: 6
        });
    }
</script>

</body>
</html>
