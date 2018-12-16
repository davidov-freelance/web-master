@extends( 'backend.layouts.app' )

@section('title', 'Users')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->


    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    {{--<script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>--}}
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>

@endsection

@section('inlineJS')
    <script type="text/javascript">

        $(function () {

            $('#datatable').DataTable({"pageLength": 25,});

            showImage();


            $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
                e.preventDefault();
                var $form = $(this);
                $('#confirm').modal({backdrop: 'static', keyboard: false})
                    .on('click', '#delete-btn', function () {
                        $form.submit();
                    });
            });

            //$(".status_change").click(function(){
            $("body").on('click', '.status_change', function () {
                // data-toggle="modal"

                $(this).attr('data-toggle', 'modal');
                var UserId = $(this).data('id');
                changeUserStatus(UserId);
            });

        });

        function changeUserStatus(UserId) {

            $('#popup_heading').html('Confirmation');
            var popupBody = '<p>Are you sure you want the change status</p>';
            $('#popup_body').html(popupBody);

            var footerData = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary" onclick="ActiveInActiveUser(' + UserId + ')">Save</button>';
            $('#PopupFooter').html(footerData);
        }

        function ManageFeature(UserId, value) {

            var message = 'Are you sure you want to remove this user from Featured User';
            if (value == 1) message = 'Are you sure you want to mark this user as Featured User';

            $('#popup_heading').html('Confirmation');
            var popupBody = '<p>' + message + '</p>';
            $('#popup_body').html(popupBody);

            var footerData = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';
            footerData += '<button type="button" class="btn btn-primary" onclick="FeatureUser(' + UserId + ',' + value + ')">Save</button>';
            $('#PopupFooter').html(footerData);
        }


        function showImage() {
            $(".group1").colorbox({height: "75%"});
        }
    </script>
@endsection

@section('content')

    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Users
                        <small></small>
                    </h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            @include('backend.layouts.modal')
            @include( 'backend.layouts.popups')
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Users
                                <small>This listing contains all users</small>
                            </h2>

                            <table border="0" cellspacing="5" cellpadding="5" style="float: right;">
                                <tbody>
                                <tr>
                                    <td style="padding:0px 10px 7px 0px">Filter By User Type:</td>
                                    <td>
                                        <div class="form-group"></div>
                                    </td>
                                </tr>

                                </tbody>
                            </table>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Badges</th>
                                    <th>Featured</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                {{--*/ $i = 1 /*--}}
                                @foreach( $users as $record )
                                    <tr class="">
                                        <td>{{ $i }}</td>
                                        <td>
                                            @if ($record->profile_picture != '')
                                                <a onclick="showImage()" class="group1"
                                                   href="{{ URL::to('/') }}/public/images/{{$record->profile_picture}}">
                                                    <img style="width:70px"
                                                         src="{{ URL::to('/') }}/public/images/{{$record->profile_picture}}"/>
                                                </a>
                                            @else
                                                <img width="70%" src="" alt="N/A"/>
                                            @endif

                                        </td>
                                        <td>{{ rtrim($record->first_name) }} </td>
                                        <td><a href="mailto:{{ $record->email }}">{{ $record->email }} </a></td>
                                        <td>
                                            @if ($record->badges)
                                            @foreach($record->badges as $badge)
                                                <div>{{$badge->name}}</div>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->is_featured == 1)
                                                <img style="width:20px"
                                                     src="http://35.160.175.165/portfolio/broadwayconnected/public/images/bluestar.png">
                                                <a style="color: #00caff; cursor: pointer" data-toggle="modal"
                                                   data-target=".bs-example-modal-sm"
                                                   onclick="ManageFeature('{{$record->id }}',0);"> Yes </a>
                                            @else
                                                <a style="color: #00caff; cursor: pointer" data-toggle="modal"
                                                   data-target=".bs-example-modal-sm"
                                                   onclick="ManageFeature('{{$record->id }}',1);"> No </a>
                                            @endif

                                        </td>


                                        <td id="CurerntStatusDiv{{ $record->id }}">

                                            @if ($record->status  === 1)
                                                <button type="button" class="btn btn-success btn-xs status_change"
                                                        data-id="{{ $record->id }}" data-target=".bs-example-modal-sm">
                                                    Active
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning btn-xs status_change"
                                                        data-id="{{ $record->id }}" data-target=".bs-example-modal-sm">
                                                    Blocked
                                                </button>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ backend_url('user/profile/'.$record->id) }}"
                                               class="btn btn-primary btn-xs" style="float: left;"><i
                                                        class="fa fa-folder"></i> View </a>
                                            <a href="{{ backend_url('user/edit/'.$record->id) }}"
                                               class="btn btn-info btn-xs edit" style="float: left;"><i
                                                        class="fa fa-pencil"></i> Edit </a>

                                            {!! Form::model($record, ['method' => 'delete', 'url' => 'backend/user/'.$record->id, 'class' =>'form-inline form-delete']) !!}
                                            {!! Form::hidden('id', $record->id) !!}
                                            {!! Form::button('<i class="fa fa-trash-o"></i> Delete ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal','data-toggle' => 'modal']) !!}
                                            {!! Form::close() !!}

                                        </td>
                                    </tr>
                                    {{--*/ $i++ /*--}}
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection