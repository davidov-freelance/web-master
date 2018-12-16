@extends( 'backend.layouts.app' )

@section('title', 'Posts')

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






    function changeStatus(id,status) {

        $('#popup_heading').html('Change Product Status');
        var popupBody = '<div class="form-group">';
            popupBody += '<label for="Select Status " class="control-label">Select Status *</label>';
            popupBody += '<select class="form-control" name="product_status" id="product_status">';
            popupBody += '<option value="pending">Pending</option>';
            popupBody += '<option value="sold">Sold</option>';
            popupBody += '<option value="approved">Approved</option>';
            popupBody += '</select>';
            popupBody += '<input type="hidden" name="post_id" id="post_id" value="'+id+'">';
            popupBody += '</div>';

        $('#popup_body').html(popupBody);
        $('#product_status').val(status);
        var footerData	 ='<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';
        footerData	    +='<button type="button" class="btn btn-primary" onclick="updateStatus('+id+',\'' + status + '\')">Save</button>';
        $('#PopupFooter').html(footerData);
    }

    </script>
@endsection

@section('content')


<div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Post/Products List <small></small></h3>
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
                    <h2>Posts <small></small></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    @include( 'backend.layouts.notification_message' )


                    <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                      <thead>
                      <tr>
                        <th>Id</th>
                        <th>Conversation Id</th>
                        <th>Sender</th>
                        <th>Message</th>
                        <th>Receiver</th>
                        <th>Last Message Date</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>

                      {{--*/ $i = 1 /*--}}
                      @foreach( $conversations as $record )

                        <tr class="">
                          <td>{{ $i }}</td>
                          <td>{{ $record->id }}</td>
                          <td>{{ $record->senderInfo['first_name'] }}</td>
                          <td>{{ $record->message }} </td>
                          <td>{{ $record->receiverInfo['first_name'] }}</td>
                          <td>{{ $record->updated_at }}</td>
                          <td>
                           <a href="{{ backend_url('conversation/messages/'.$record->id) }}" class="btn btn-primary btn-xs" style="float: left;"><i class="fa fa-folder"></i> View Message </a>


                            {!! Form::model($record, ['method' => 'delete', 'url' => 'backend/posts/remove/'.$record->id, 'class' =>'form-inline form-delete']) !!}
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