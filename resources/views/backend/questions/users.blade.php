@extends( 'backend.layouts.app' )

@section('title', 'Users')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{backend_asset('libraries/datatables-responsive/dataTables.responsive.css')}}" rel="stylesheet">
    <link href="{{backend_asset('libraries/galleria/colorbox.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    {{--<script src="{{backend_asset('libraries/datatables/js/jquery.dataTables.min.js')}}"></script>--}}
    <script src="{{backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{backend_asset('libraries/datatables-responsive/dataTables.responsive.js')}}"></script>
<script src="{{backend_asset('libraries/galleria/jquery.colorbox.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">

        $(function () {
            $('#datatable').DataTable({ "pageLength": 25, });
        });

        function showImage() {
            $(".group1").colorbox({ height: "75%" });
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

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                <tr>
                                    <th>User Id</th>
                                    <th>Image</th>
                                    <th>User Email</th>
                                    <th>User Name</th>
                                    <th>Genius Streak</th>
                                    <th>Geek Streak</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach( $users as $record )
                                    <tr class="">
                                        <td>{{$record->id}}</td>
                                        <td>
                                            @if ($record->profile_picture != '')
                                                <a onclick="showImage()" class="group1" href="{{URL::to('/')}}/public/images/{{$record->profile_picture}}">
                                                    <img style="width:70px" src="{{URL::to('/')}}/public/images/{{$record->profile_picture}}" />
                                                </a>
                                            @else
                                                <img width="70%" src="" alt="N/A" />
                                            @endif
                                        </td>
                                        <td><a href="mailto:{{$record->email}}">{{$record->email}} </a> </td>
                                        <td>{{rtrim($record->first_name)}} </td>
                                        <td>{{rtrim($record->genius_streak)}} </td>
                                        <td>{{rtrim($record->geek_streak)}} </td>
                                    </tr>
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