<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Broadway Connected :: @yield('title')</title>

	    <!-- Bootstrap -->
    <link href="{{ backend_asset('libraries/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ backend_asset('libraries/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ backend_asset('libraries/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ backend_asset('libraries/iCheck/skins/flat/green.css') }}" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="{{ backend_asset('libraries/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">
    <!-- JQVMap -->
    {{--<link href="{{ backend_asset('libraries/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>--}}
    <!-- bootstrap-daterangepicker -->
    <link href="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ backend_asset('css/custom.min.css') }}" rel="stylesheet">


    <!-- Confirm Alert CSS -->
    <link href="{{ backend_asset('css/jquery-confirm.css') }}" rel="stylesheet">


@yield('CSSLibraries')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">

   <!-- <div id="wrapper">-->
    <?php /*?>@if ( Auth::check() )<?php */?>
        <!-- Navigation -->
		 @include('backend.layouts.sidebar')
		 @include('backend.layouts.post_form')

        <!-- Navigation [END] -->
   <?php /*?> @endif<?php */?>

    @yield('content')

    </div>
	</div>
    <!-- /#wrapper -->


    <!-- jQuery -->
    <script src="{{ backend_asset('libraries/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ backend_asset('libraries/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ backend_asset('libraries/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ backend_asset('libraries/nprogress/nprogress.js') }}"></script>
    <!-- Chart.js -->
    {{--<script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>--}}
    <!-- gauge.js -->
    {{--<script src="{{ backend_asset('libraries/gauge.js/dist/gauge.min.js') }}"></script>--}}
    <!-- bootstrap-progressbar -->
    <script src="{{ backend_asset('libraries/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ backend_asset('libraries/iCheck/icheck.min.js') }}"></script>

    <!-- DateJS -->
    <script src="{{ backend_asset('libraries/DateJS/build/date.js') }}"></script>
    <!-- JQVMap -->
    {{--<script src="{{ backend_asset('libraries/jqvmap/dist/jquery.vmap.js')}}"></script>
    <script src="{{ backend_asset('libraries/jqvmap/dist/maps/jquery.vmap.world.js')}}"></script>
    <script src="{{ backend_asset('libraries/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script>--}}
    <!-- bootstrap-daterangepicker -->
    <script src="{{ backend_asset('libraries/moment/min/moment.min.js')}}"></script>
    <script src="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ backend_asset('js/jquery-confirm.js') }}"></script>

    <script src="{{ backend_asset('js/config-js.js') }}"></script>
    <script src="{{ backend_asset('js/custom-functions.js') }}"></script>
    <script src="{{ backend_asset('libraries/jquery/dist/jquery-ui.min.js') }}"></script>

    @yield('JSLibraries')

    <!-- Custom Theme JavaScript -->
    <script src="{{ backend_asset('js/custom.min.js')}}"></script>
    <script>
        // Send Token in All AJAX Requests
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        // Set timezone offset in cookies
        setTimezoneOffsetInCookie();
    </script>

@yield('inlineJS')


</body>

</html>





