@extends( 'backend.layouts.app' )

@section('title', 'Users')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
    </script>

@endsection

@section('content')

    <style>
        .degree {

            width: 95%;
            margin-left: -10px;
            padding: 10px;
        }

    </style>
    <?php
    $ref_name_1 = $ref_phone_1 = $email_1 = $relation_1 = 'N/A';
    $ref_name_2 = $ref_phone_2 = $email_2 = $relation_2 = 'N/A';

    //echo "<pre>";
    // dd($users);
    $refernceCount = count($users->references);
    $refernces = $users->references;

    $teachingLevelsCount = count($users->teachingLevels);
    $teachingLevels = $users->teachingLevels;

    $headline_position = isset($users->headline_position) ? $users->headline_position : 0;
    $profile_image = isset($users->profile_image) ? $users->profile_image : 'N/A';

    $first_name = isset($users->first_name) ? $users->first_name : 'N/A';
    $last_name = isset($users->last_name) ? $users->last_name : 'N/A';
    $dob = isset($users->dob) ? $users->dob : 'N/A';
    $country = isset($users->country) ? $users->country : 'N/A';
    $city = isset($users->city) ? $users->city : 'N/A';

    $previous_position = isset($users->previous_position) ? $users->previous_position : 'N/A';
    $is_featured = isset($users->is_featured) ? $users->is_featured : 'N/A';
    $headline_position = isset($users->headline_position) ? $users->headline_position : 'N/A';
    $field_of_work = isset($users->field_of_work) ? $users->field_of_work : 'N/A';
    $handle = isset($users->handle) ? $users->handle : 'N/A';
    $is_verified = isset($users->is_verified) ? $users->is_verified : 'N/A';
    $verification_code = isset($users->verification_code) ? $users->verification_code : 'N/A';
    $email = isset($users->email) ? $users->email : 'N/A';
    $biography = isset($users->biography) ? $users->biography : 'N/A';

    $article_count = isset($users->article_count) ? $users->article_count : '0';
    $event_count = isset($users->event_count) ? $users->event_count : '0';
    $following_count = isset($users->following_count) ? $users->following_count : '0';
    $follower_count = isset($users->follower_count) ? $users->follower_count : '0';


    $device_type = isset($users->device_type) ? $users->device_type : 'N/A';
    $device_token = isset($users->device_token) ? $users->device_token : 'N/A';
    $notification_status = isset($users->notification_status) ? $users->notification_status : 'N/A';


    ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>{{$users['full_name']}} Profile</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Profile Detail
                                <small></small>
                            </h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                                <div class="profile_img">
                                    <div id="crop-avatar">
                                        <!-- Current avatar -->
                                        <img class="img-responsive avatar-view" src="{{ $users->profile_image }}"
                                             alt="Avatar" title="Change the avatar">
                                    </div>
                                </div>
                                <h3>{{$users['first_name']}}</h3>

                                <ul class="list-unstyled user_data">
                                    <li><label>Handle :</label>{{$handle}}</li>
                                    <li><label>Field Of Work :</label>{{$field_of_work}}</li>
                                    <li><label>Status: </label>
                                        <b>{{ $users['status'] == 1 ? 'Active' : 'inactive'}}</b></li>
                                </ul>

                                {{--<a class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Edit Profile</a>--}}
                                <br/>


                                <div class="col-md-12 col-sm-12 col-xs-12 profile_details">
                                    <div style="padding: 10px;" class="well profile_view degree">

                                        <h4 class="brief"><i>Quick Analytics</i></h4>
                                        <div style="margin-top: 10px;">

                                            <p><strong>Articles Posted: </strong>
                                                <a style="color: #00caff"
                                                   href="{{ backend_url('articles/'.$users->id) }}">{{ $article_count }}</a>
                                            </p>

                                            <p><strong>Event Posted : </strong> <a style="color: #00caff"
                                                                                   href="{{ backend_url('events/'.$users->id) }}">{{ $event_count }}</a>
                                            </p>
                                            <p><strong>Following Count : </strong> {{  $following_count }} </p>
                                            <p><strong>Followers Count : </strong> {{  $follower_count }} </p>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">

                                <div class="profile_title">
                                    <div class="col-md-12">
                                        <h2>{{ $first_name.' '.$last_name }} Profile</h2>
                                    </div>

                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <!-- start of user-activity-graph -->
                            {{--<div  style="width:100%; height:320px;">--}}




                            {{--</div>--}}

                            <!-- end of user-activity-graph -->

                                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist" style="float: left">
                                        <li role="presentation" class="active"><a href="#tab_content1" id="home-tab"
                                                                                  role="tab" data-toggle="tab"
                                                                                  aria-expanded="true">Other Info</a>
                                        </li>
                                        <li role="presentation" class=""><a href="#tab_content2" role="tab"
                                                                            id="profile-tab" data-toggle="tab"
                                                                            aria-expanded="false">Settings</a>
                                        </li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content1"
                                             aria-labelledby="home-tab">


                                            <!-- start user projects -->
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th colspan="2">Other Detail</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><label>First Name </label></td>
                                                    <td>{{$first_name}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>Last Name </label></td>
                                                    <td>{{$last_name}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>Date Of Birth </label></td>
                                                    <td>{{$dob}}</td>
                                                </tr>

                                                <tr>
                                                    <td style="width: 30%;"><label>Email Address </label></td>
                                                    <td>{{$email}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>Country </label></td>
                                                    <td>{{$country}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>City </label></td>
                                                    <td>{{$city}}</td>
                                                </tr>

                                                <tr>
                                                    <td style="width: 30%;"><label>Field </label></td>
                                                    <td>{{$field_of_work}}</td>
                                                </tr>

                                                <tr>
                                                    <td style="width: 30%;"><label>Previous Position </label></td>
                                                    <td>{{$previous_position}}</td>
                                                </tr>

                                                <tr>
                                                    <td style="width: 30%;"><label>Headline Position </label></td>
                                                    <td>{{$headline_position}}</td>
                                                </tr>

                                                <tr>
                                                    <td style="width: 30%;"><label>Is Featured </label></td>
                                                    <td>{{$is_featured == 1 ? 'YES' : 'No'}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>Created</label></td>
                                                    <td>{{Carbon\Carbon::parse($users['created_at'])->format('d-M-Y H:i:s')}}</td>
                                                </tr>

                                                <tr>
                                                    <td><label>Badges</label></td>
                                                    <td>
                                                        @if(count($users->badges))
                                                            @foreach ($users->badges as $badge)
                                                                <div class="profile-badge-row">
                                                                    <img src="{{$badge->badge_icon}}" alt="{{$badge->name}}" class="profile-badge-icon"> -
                                                                    {{$badge->name}} - {{$badge->badge_amount}}
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>

                                            <!-- end user projects -->

                                        </div>

                                        <div role="tabpanel" class="tab-pane fade" id="tab_content2"
                                             aria-labelledby="profile-tab">


                                            <!-- start user projects -->
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th colspan="2"> Setting</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><label>Push Notifications</label></td>
                                                    <td>{{$notification_status == 1 ? 'ON' : 'OFF' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><label> Platform</label></td>
                                                    <td>{{$device_type == '' ? 'N/A' : $device_type }}</td>
                                                </tr>
                                                <tr>
                                                    <td><label>Device Token</label></td>
                                                    <td>{{ $device_token =='' ? 'N/A' : $device_token }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <!-- end user projects -->
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection