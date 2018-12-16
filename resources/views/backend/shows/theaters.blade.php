@extends( 'backend.layouts.app' )

@section('title', 'Theaters')

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
            var $dataTable = $('#datatable');
            var popupWasInitialized = false;
            var $popup = $('.bs-example-modal-sm');
            $dataTable.dataTable();

            $dataTable.on('click', '.form-delete', function (elem) {
                elem.preventDefault();
                var $form = $(this);
                var hasShow = $form.data('has-show');

                if (hasShow) {
                    showFeatureDisabledPopup();
                    return;
                }

                showDeletionPopup($form);
            });

            function showFeatureDisabledPopup() {
                if (!popupWasInitialized) {
                    initPopup();
                    popupWasInitialized = true;
                }

                $popup.modal({ backdrop: 'static', keyboard: false });
            }

            function initPopup() {
                var popupHeader = 'Error Deleting Theater';
                var popupBody = '<div class="form-group">' +
                    'For now this feature is disabled. Cannot delete a theater that relates to at least one show. ' +
                    'For more details please contact Phil Scarfi' +
                    '</div>';

                var popupButton = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';

                $popup.find('#popup_heading').html(popupHeader);
                $popup.find('#popup_body').html(popupBody);
                $popup.find('#PopupFooter').html(popupButton);
            }

            function showDeletionPopup($form) {
                $('#confirm').modal({ backdrop: 'static', keyboard: false })
                    .on('click', '#delete-btn', function () {
                        $form.submit();
                    });
            }

            $(".group1").colorbox({ height: "75%" });
        });
    </script>
@endsection

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Theaters List <small></small></h3>
                </div>
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group"></div>
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
                            <h2>Theaters <small></small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @include( 'backend.layouts.notification_message' )
                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Theater Name</th>
                                        <th>Capacity</th>
                                        <th>Location</th>
                                        <th>Zip</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--*/ $i = 1 /*--}}
                                    @foreach ($theaters as $theater)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$theater->name}}</td>
                                            <td>{{$theater->capacity}}</td>
                                            <td>{{$theater->location}}</td>
                                            <td>{{$theater->zip}}</td>
                                            <td>
                                                <a href="{{backend_url('shows/theaters/edit/' . $theater->id)}}" class="btn btn-info btn-xs edit-button">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                {{Form::model($theater, ['method' => 'delete', 'url' => 'backend/shows/theaters/remove/' . $theater->id, 'class' => 'form-inline form-delete', 'data-has-show' => $theater->has_show])}}
                                                {{Form::hidden('id', $theater->id) }}
                                                {{Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'data-toggle' => 'modal'])}}
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