@extends( 'backend.layouts.app' )

@section('title', 'Cateories')

@section('CSSLibraries')
        <!-- DataTables CSS -->
<link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

<!-- DataTables Responsive CSS -->
<link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">

<!-- Image Viewer CSS -->
<link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">


@endsection

@section('JSLibraries')
        <!-- DataTables JavaScript -->
<script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
<script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>



@endsection

@section('inlineJS')

    <script type="text/javascript">

        $(document).ready(function() {
            $('#datatable').dataTable();
            $(".group1").colorbox({height:"75%"});

        });


        $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
            e.preventDefault();
            var $form = $(this);
            $('#confirm').modal({backdrop: 'static', keyboard: false})
                    .on('click', '#delete-btn', function () {
                        $form.submit();
                    });
        });


        function showImage() {
            $(".group1").colorbox({height:"75%"});
        }


    </script>
@endsection

@section('content')


    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Badges List <small></small></h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group">

                        </div>
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
                            <h2>Badges <small></small></h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )


                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Icon</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach( $badges as $index => $record )
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($record->badge_icon != '')
                                                <a onclick="showImage()" class="group1" href="{{$record->badge_icon}}">
                                                    <img style="width:70px" src="{{$record->badge_icon}}" />
                                                </a>
                                            @else
                                                <img width="70%" src="" alt="N/A" />
                                            @endif

                                        </td>

                                        <td>{{ $record->name }}</td>
                                        <td>{{ $record->created_at }}</td>
                                        <td>
                                            <a href="{{ backend_url('badges/edit/'.$record->id) }}" class="btn btn-info btn-xs edit" style="float: left;"><i class="fa fa-pencil"></i> </a>

                                            {!! Form::model($record, ['method' => 'delete', 'url' => 'backend/badges/' .$record->id, 'class' =>'form-inline form-delete']) !!}
                                            {!! Form::hidden('id', $record->id) !!}
                                            {!! Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal', 'data-toggle' => 'modal']) !!}
                                            {!! Form::close() !!}

                                        </td>
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