@extends( 'backend.layouts.app' )

@section('title', 'Groups')

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

        $(document).ready(function() {
            $('#datatable').dataTable();
        });

    </script>
@endsection

@section('content')
    <style>
        .group-members {

        }
    </style>

    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Group <small>Listing of all created groups</small></h3>
                </div>


            </div>

            <div class="clearfix"></div>

            {{--@include('backend.layouts.modal')--}}

            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Groups <small>  listing</small></h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Members</th>
                                    <th>Action</th>
                                </tr>
                                </thead>


                                <tbody>
                                @foreach( $groups as $record )
                                    <tr class="">
                                        <td>{{ $record->id }}</td>
                                        <td>{{ $record->name }}</td>

                                        @if(count($record->member) > 0 )
                                            <td>
                                                <div class="group-members">

                                                    @foreach($record->member as $req)
                                                        <span class="btn btn-info">
                                                            {{ isset($req->user->first_name) ? $req->user->first_name : 'N/A'}} </span>
                                                    @endforeach
                                                </div>

                                            </td>
                                        @else
                                            <td>  No Member  </td>
                                        @endif


                                        <td>

                                            <a href="{{ backend_url('articles/group/'.$record->id) }}" class="btn btn-xs btn-primary edit" style="float: left;">Articles By Group</a>
                                            <a href="{{ backend_url('events/group/'.$record->id) }}" class="btn btn-xs btn-primary edit" style="float: left;">Events By Group</a>

                                            <a href="{{ backend_url('group/edit/'.$record->id) }}" class="btn btn-xs btn-primary edit" style="float: left;"><i class="fa fa-pencil"></i></a>

                                            {!! Form::model($record, ['method' => 'delete', 'url' => 'backend/group/'.$record->id, 'class' =>'form-inline form-delete']) !!}
                                            {!! Form::hidden('id', $record->id) !!}
                                            {!! Form::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-xs btn-danger delete', 'name' => 'delete_modal', 'type' => 'submit']) !!}
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