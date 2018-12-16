@extends( 'backend.layouts.app' )

@section('title', 'Shows')

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
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        $(function () {
            var tableSelector = '#datatable';
            $(tableSelector).dataTable();
            initDeleteForm(tableSelector, '.form-delete');
            $(".group1").colorbox({ height: "75%" });
        });
    </script>
@endsection

@section('content')
    @include('backend.layouts.modal')
    @include( 'backend.layouts.popups')
    <div class="right_col" role="main">
        <div>
            <div class="page-title">
                <div class="title_left">
                    <h3>Shows List</h3>
                </div>
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Shows</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Show name</th>
                                        <th>Image</th>
                                        <th>Preview At</th>
                                        <th>Opening Night At</th>
                                        <th>Engagement At</th>
                                        <th>Closing At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--*/ $i = 1 /*--}}
                                    @foreach ($shows as $show)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$show->name}}</td>
                                            <td>
                                                @if ($show->show_image)
                                                    <a class="group1" href="{{$show->show_image}}" title={{$show->name}}>
                                                        <img class="table-image" src="{{$show->show_image}}" />
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{$show->preview_at}}</td>
                                            <td>{{$show->opening_night_at}}</td>
                                            <td>{{$show->engagement_at}}</td>
                                            <td>{{$show->closing_at}}</td>
                                            <td>
                                                <a href="{{backend_url('shows/gross/' . $show->id)}}" class="btn btn-warning btn-xs gross-button">
                                                    <i class="fa fa-usd"></i>
                                                </a>
                                                <a href="{{backend_url('shows/edit/' . $show->id)}}" class="btn btn-info btn-xs edit-button">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                {{Form::model($show, ['method' => 'delete', 'url' => 'backend/shows/remove/' . $show->id, 'class' => 'form-inline form-delete'])}}
                                                {{Form::hidden('id', $show->id) }}
                                                {{Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal', 'data-toggle' => 'modal'])}}
                                                {{Form::close()}}
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