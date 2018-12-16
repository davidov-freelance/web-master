@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')


@section('CSSLibraries')
  <link href="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
  <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>

  <script src="{{ backend_asset('libraries/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
  <script src="{{ backend_asset('libraries/skycons/skycons.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot/jquery.flot.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot/jquery.flot.pie.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot/jquery.flot.time.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot/jquery.flot.stack.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot/jquery.flot.resize.js')}}"></script>

  <script src="{{ backend_asset('libraries/flot.orderbars/js/jquery.flot.orderBars.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot-spline/js/jquery.flot.spline.min.js')}}"></script>
  <script src="{{ backend_asset('libraries/flot.curvedlines/curvedLines.js')}}"></script>

  <script src="{{ backend_asset('libraries/DateJS/build/date.js')}}"></script>
  <script src="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
@endsection

@section('inlineJS')
  <script>
    


    $(document).ready(function() {
      //random data
      var d1 = [
        [0, 1],
        [1, 9],
        [2, 6],
        [3, 10],
        [4, 5],
        [5, 17],
        [6, 6],
        [7, 10],
        [8, 7],
        [9, 11],
        [10, 35],
        [11, 9],
        [12, 12],
        [13, 5],
        [14, 3],
        [15, 4],
        [16, 9]
      ];

      //flot options
      var options = {
        series: {
          curvedLines: {
            apply: true,
            active: true,
            monotonicFit: true
          }
        },
        colors: ["#26B99A"],
        grid: {
          borderWidth: {
            top: 0,
            right: 0,
            bottom: 1,
            left: 1
          },
          borderColor: {
            bottom: "#7F8790",
            left: "#7F8790"
          }
        }
      };
      var plot = $.plot($("#placeholder3xx3"), [{
        label: "Registrations",
        data: d1,
        lines: {
          fillColor: "rgba(150, 202, 89, 0.12)"
        }, //#96CA59 rgba(150, 202, 89, 0.42)
        points: {
          fillColor: "#fff"
        }
      }], options);
    });
    $(document).ready(function() {
      $(".sparkline_one").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
        type: 'bar',
        height: '40',
        barWidth: 9,
        colorMap: {
          '7': '#a1a1a1'
        },
        barSpacing: 2,
        barColor: '#26B99A'
      });

      $(".sparkline_two").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
        type: 'line',
        width: '200',
        height: '40',
        lineColor: '#26B99A',
        fillColor: 'rgba(223, 223, 223, 0.57)',
        lineWidth: 2,
        spotColor: '#26B99A',
        minSpotColor: '#26B99A'
      });
    });
    $(document).ready(function() {
      var options = {
        legend: false,
        responsive: false
      };


    });
    $(document).ready(function() {

      var cb = function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      };

      var optionSet1 = {
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '01/01/2017',
        maxDate: '12/31/2019',
        dateLimit: {
          days: 60
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'left',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        locale: {
          applyLabel: 'Submit',
          cancelLabel: 'Clear',
          fromLabel: 'From',
          toLabel: 'To',
          customRangeLabel: 'Custom',
          daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
          monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          firstDay: 1
        }
      };
      $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
      $('#reportrange').daterangepicker(optionSet1, cb);
      $('#reportrange').on('show.daterangepicker', function() {
        console.log("show event fired");
      });
      $('#reportrange').on('hide.daterangepicker', function() {
        console.log("hide event fired");
      });
      $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
        showDynamicStats(picker.startDate.format('YYYY-MM-D 00:00:00'),picker.endDate.format('YYYY-MM-D 23:59:59'));
      });
      $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
        console.log("cancel event fired");
      });
      $('#options1').click(function() {
        $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
      });
      $('#options2').click(function() {
        $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
      });
      $('#destroy').click(function() {
        $('#reportrange').data('daterangepicker').remove();
      });
    });
    $(document).ready(function() {

      $MENU_TOGGLE.on('click', function() {
        $(window).resize();
      });
    });

    var icons = new Skycons({
              "color": "#73879C"
            }),
            list = [
              "clear-day", "clear-night", "partly-cloudy-day",
              "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
              "fog"
            ],
            i;

    for (i = list.length; i--;)
      icons.set(list[i], list[i]);

    icons.play();



    $( document ).ready(function() {

      showStats();
      showDynamicStats(moment().subtract(29, 'days').format('YYYY-MM-D 00:00:00'),moment().format('YYYY-MM-D 23:59:59'));
    });


  </script>
@endsection


@section('content')

  <style>
    /*.dashboard_graph {*/
    /*background: #1ABB9C;*/
    /*padding: 8px 10px;*/
    /*color: #fff;*/
    /*}*/
    /*.tile-stats {*/
    /*border:1px solid #1ABB9C !important;*/
    /*}*/

    .tile-stats .icon i { font-size: 40px; }
    .tile-stats .icon { top:10px; right: 30px; }

  </style>
  <div class="right_col" role="main">
    <div class="">
      <div class="row top_tiles">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-group" style="color: #F5B7B1;"></i></div>
            <div class="count" id="totalUser">0</div>
            <h3>Total Users</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-facebook" style="color:#1b5Ac3"></i></div>
            <div class="count" id="totalFbUser">0</div>
            <h3>Total Fb Users</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-user" style="color:#E95E4F"></i></div>
            <div class="count" id="totalModerator">0</div>
            <h3>Total Moderator</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-user" style="color:#E95E4F"></i></div>
            <div class="count" id="totalSubAdmins">0</div>
            <h3>Total Sub Admins</h3>
          </div>
        </div>

      </div>

      <div class="row top_tiles">


        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-newspaper-o" style="color:#9B59B6"></i></div>
            <div class="count" id="totalArticles">0</div>
            <h3>Total Articles</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-glass" style="color:#36CAAB"></i></div>
            <div class="count" id="totalEvents">0</div>
            <h3>Total Events</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-tags" style="color:#FBBc2F"></i></div>
            <div class="count" id="totalTag">0</div>
            <h3>Total Tag</h3>
          </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-bars" style="color:#36CAAB"></i></div>
            <div class="count" id="totalCategories">0</div>
            <h3>Total Categories</h3>
          </div>
        </div>

      </div>
      <br />


      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="dashboard_graph x_panel">
            <div class="row x_title">
              <div class="col-md-6">
                <h3>Broadway Timely Statistics <small style="color:#fff;"> Filter Stats By Date </small></h3>
              </div>
              <div class="col-md-6">
                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                  <span>December 30, 2016 - January 28, 2017</span> <b class="caret"></b>
                </div>
              </div>
            </div>
            <div class="x_content">
              <div class="demo-container" style="height:250px; display: none">

                <div id="placeholder3xx3" class="demo-placeholder" style="width: 100%; height:250px;display: none"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- top tiles -->

      <div class="row top_tiles">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-star" style="color: #1ABB9C;"></i></div>
            <div class="count" id="totalPosts">0</div>
            <h3>Total Post</h3>
          </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-newspaper-o"></i></div>
            <div class="count" id="total_articles">0</div>
            <h3>Total Articles</h3>
          </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-glass"></i></div>
            <div class="count" id="total_events">0</div>
            <h3>Total Events</h3>
          </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-comments"></i></div>
            <div class="count" id="totalFeedbacks">0</div>
            <h3>Feedbacks</h3>
          </div>
        </div>
      </div>

      <div class="row top_tiles">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-money"></i></div>
            <div class="count" id="total_users">0</div>
            <h3>Total Users</h3>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="tile-stats">
            <div class="icon"><i class="fa fa-bars"></i></div>
            <div class="count" id="fb_users">0</div>
            <h3>Fb Users</h3>
          </div>
        </div>
      </div>
      <!-- /top tiles -->

      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="x_panel fixed_height_320">
            <div class="x_title">
              <h2>Posts Count<small>Bar Graph</small></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                  </ul>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              {{--<h4>Orders By Percentage</h4>--}}
              <div class="widget_summary">
                <div class="w_left w_25">
                  <span>Event Posted</span>
                </div>
                <div class="w_center w_55">
                  <div class="progress">
                    <div id="event_per" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 66%; background-color: #FBBc2F;">

                    </div>
                  </div>
                </div>
                <div class="w_right w_20">
                  <span id="event_count"></span>
                </div>
                <div class="clearfix"></div>
              </div>

              <div class="widget_summary">
                <div class="w_left w_25">
                  <span>Article Posted</span>
                </div>
                <div class="w_center w_55">
                  <div class="progress">
                    <div id="articles_per"  class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 45%;background-color: #1b5Ac3;">

                    </div>
                  </div>
                </div>
                <div class="w_right w_20">
                  <span id="articles_count"></span>
                </div>
                <div class="clearfix"></div>
              </div>


            </div>
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="x_panel fixed_height_320">
            <div class="x_title">
              <h2>Posts Stats <small>Pie Chart Representation</small></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                  </ul>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <table class="" style="width:100%">
                <tr>
                  <th style="width:37%;">
                    <p></p>
                  </th>
                  <th>
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                      <p class=""></p>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                      <p class=""></p>
                    </div>
                  </th>
                </tr>
                <tr>
                  <td>
                    <canvas id="canvas1" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                  </td>
                  <td>
                    <table class="tile_info">
                      <tr>
                        <td>
                          <p><i class="fa fa-square" style="color: #1b5Ac3"></i>Articles </p>
                        </td>
                        <td id="andUserPer">0%</td>
                      </tr>
                      <tr>
                        <td>
                          <p><i class="fa fa-square green" style="color: #9B59B6"></i>Events </p>
                        </td>

                        <td id="iosUserPer">0%</td>
                      </tr>

                    </table>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        {{--<div class="col-md-4 col-sm-6 col-xs-12">--}}
        {{--<div class="x_panel fixed_height_320">--}}
        {{--<div class="x_title">--}}
        {{--<h2>Profile Settings <small>Sessions</small></h2>--}}
        {{--<ul class="nav navbar-right panel_toolbox">--}}
        {{--<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>--}}
        {{--</li>--}}
        {{--<li class="dropdown">--}}
        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>--}}
        {{--<ul class="dropdown-menu" role="menu">--}}
        {{--<li><a href="#">Settings 1</a>--}}
        {{--</li>--}}
        {{--<li><a href="#">Settings 2</a>--}}
        {{--</li>--}}
        {{--</ul>--}}
        {{--</li>--}}
        {{--<li><a class="close-link"><i class="fa fa-close"></i></a>--}}
        {{--</li>--}}
        {{--</ul>--}}
        {{--<div class="clearfix"></div>--}}
        {{--</div>--}}
        {{--<div class="x_content">--}}
        {{--<div class="dashboard-widget-content">--}}
        {{--<ul class="quick-list">--}}
        {{--<li><i class="fa fa-line-chart"></i><a href="#">Achievements</a></li>--}}
        {{--<li><i class="fa fa-thumbs-up"></i><a href="#">Favorites</a></li>--}}
        {{--<li><i class="fa fa-calendar-o"></i><a href="#">Activities</a></li>--}}
        {{--<li><i class="fa fa-cog"></i><a href="#">Settings</a></li>--}}
        {{--<li><i class="fa fa-area-chart"></i><a href="#">Logout</a></li>--}}
        {{--</ul>--}}

        {{--<div class="sidebar-widget">--}}
        {{--<h4>Profile Completion</h4>--}}
        {{--<canvas width="150" height="80" id="foo" class="" style="width: 160px; height: 100px;"></canvas>--}}
        {{--<div class="goal-wrapper">--}}
        {{--<span id="gauge-text" class="gauge-value pull-left">0</span>--}}
        {{--<span class="gauge-value pull-left">%</span>--}}
        {{--<span id="goal-text" class="goal-value pull-right">100%</span>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}


      </div>
    </div>
  </div>


@endsection