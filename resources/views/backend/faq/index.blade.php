@extends( 'backend.layouts.app' )

@section('title', 'Help Content')

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
$(document).ready(function () {
    $('#datatable').dataTable();

    $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#confirm').modal({backdrop: 'static', keyboard: false})
                .on('click', '#delete-btn', function () {
                    $form.submit();
                });
    });
});


</script>
@endsection

@section('content')

<div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Faq <small>Content</small></h3>
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

                  <div class="x_content">

                    @include( 'backend.layouts.notification_message' )


                    <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                      <thead>
                      <tr>
                        <th>Content Type</th>
                        <th>Content</th>
                        <th>Action</th>
                      </tr>
                      </thead>


                      <tbody>

                        <tr class="">

                          <td>Faq</td>
                          <td>
                              @foreach( $help_content as $record )

                              {{ $record->heading }}<br>
                              <?php  printf(nl2br($record->description)); ?> <br>

                              @endforeach
                          </td>

                            <td>
                                <a href="{{ backend_url('faq/edit/'.$record->key) }}" class="btn btn-info btn-xs edit"><i class="fa fa-pencil"></i> Edit </a>
                            </td>

                        </tr>

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