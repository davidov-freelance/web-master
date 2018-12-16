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
    <!-- Datatable -->
      $(document).ready(function() {
      $('#datatable').dataTable();
    });

    </script>
@endsection

@section('content')


<div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Cms <small>Listing of all static pages</small></h3>
              </div>


            </div>

            <div class="clearfix"></div>

              {{--@include('backend.layouts.modal')--}}

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Cms <small> Page listing</small></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    @include( 'backend.layouts.notification_message' )

                    <table id="datatable" class="table table-striped table-bordered">
                      <thead>
                      <tr>
                        <th>Page Key</th>
                        <th>Title</th>
                          <th style=" max-width:700px;">body</th>

                        <th>Action</th>
                      </tr>
                      </thead>


                      <tbody>
                      @foreach( $pages as $record )
                        <tr class="">
                          <td>{{ rtrim($record->key) }}</td>
                          <td>{{ $record->title }}</td>
                            <td style="max-width:700px;float:left;word-wrap: break-word;">{{ $record->body }}</td>

                          <td>
                            <a href="{{ backend_url('cms/edit/'.$record->id) }}" class="btn btn-xs btn-primary edit" style="float: left;"><i class="fa fa-pencil"></i></a>

                            {{--{!! Form::model($record, ['method' => 'delete', 'url' => 'backend/cms/'.$record->id, 'class' =>'form-inline form-delete']) !!}--}}
                            {{--{!! Form::hidden('id', $record->id) !!}--}}
                            {{--{!! Form::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-xs btn-danger delete', 'name' => 'delete_modal', 'type' => 'submit']) !!}--}}
                            {{--{!! Form::close() !!}--}}
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