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
                    <h3>Categories List <small></small></h3>
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
                            <h2>Categories <small></small></h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )


                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th style="width: 40%">Category</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>


                                <tbody>

                                {{--*/ $i = 1 /*--}}
                                @foreach( $categories as $record )



                                    <tr class="">
                                        <td>{{ $i }}</td>
                                        <td>{{ $record->category_name }}</td>
                                        <td>{{ $record->sort_order }}</td>

                                        <td>
                                            @if ($record->status  == '1') {{--*/ $statusClass = 'btn-success'; $statusText = 'Active' /*--}}
                                            @elseif ($record->status  == '0') {{--*/ $statusClass = 'btn-danger' ; $statusText = 'Inactive' /*--}}
                                            @endif
                                            <button  type="button" class="btn {{ $statusClass }}  btn-xs "  > {{ $statusText }}</button>
                                        </td>



                                        <td>{{ $record->created_at }}</td>
                                        <td>
                                            <a href="{{ backend_url('categories/edit/'.$record->id) }}" class="btn btn-info btn-xs edit" style="float: left;"><i class="fa fa-pencil"></i> </a>

                                            {!! Form::model($record, ['method' => 'delete', 'url' => 'backend/categories/'.$record->id, 'class' =>'form-inline form-delete']) !!}
                                            {!! Form::hidden('id', $record->id) !!}
                                            {!! Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal','data-toggle' => 'modal']) !!}
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