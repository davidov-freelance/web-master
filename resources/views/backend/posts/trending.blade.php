@extends( 'backend.layouts.app' )
@section('title', 'Trending')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">

    <!-- TimePicker CSS -->
    <link href="{{ backend_asset('libraries/jquery/dist/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.js') }}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        function showImage() {
            $(".group1").colorbox({ height: "75%" });
        }

        function changeStatus(id, status) {

            $('#popup_heading').html('Change Status');
            var popupBody = '<div class="form-group">';
            popupBody += '<label for="Select Status " class="control-label">Select Status *</label>';
            popupBody += '<select class="form-control" name="product_status" id="product_status">';
            popupBody += '<option value="approved">Published</option>';
            popupBody += '<option value="pending">Un Published</option>';
            popupBody += '</select>';
            popupBody += '<input type="hidden" name="post_id" id="post_id" value="' + id + '">';
            popupBody += '</div>';

            $('#popup_body').html(popupBody);
            $('#product_status').val(status);
            var footerData = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';
            footerData += '<button type="button" class="btn btn-primary" onclick="updateStatus(' + id + ',\'' + status + '\')">Save</button>';
            $('#PopupFooter').html(footerData);
        }

        function repost(id) {

            $('#popup_heading').html('Repost Article');
            var popupBody = '<div class="form-group">';
            popupBody += '<label for="Select Status " class="control-label">Event Repost : Event published date will change to current date and post will start appearing at top of article list as we are displaying most recent first. </label><br>';
            popupBody += '<label for="Select Status " class="control-label">Are you sure , you want to repost this event? </label>';
            popupBody += '</div>';

            $('#popup_body').html(popupBody);
            $('#product_status').val(status);
            var footerData = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';
            footerData += '<button type="button" class="btn btn-primary" onclick="repostPost(' + id + ')">Repost</button>';
            $('#PopupFooter').html(footerData);
        }

        function rebuildTable($table, exemplar) {
            exemplar && exemplar.fnDestroy();
            return $table.dataTable({
                order: [8, 'desc'],
            });
        }

        $(function () {
            var tableSelector = '#datatable';
            var $datatable = $(tableSelector);
            var dataTable = rebuildTable($datatable);

            $('.group1').colorbox({ height: "75%" });

            $(tableSelector).on('focusout', '.editabled', function () {
                var $currentElem = $(this);
                var orderNumberString = $currentElem.text();
                var orderNumber = parseInt(orderNumberString) || 0;
                if (orderNumber < 0) {
                    orderNumber = 0;
                }
                var oldOrderNumber = $currentElem.data('order');
                $currentElem.text(orderNumber);

                if (orderNumber === oldOrderNumber) {
                    return;
                }

                var id = $currentElem.data('id');

                $.ajax({
                    type: 'POST',
                    url: AJAX_URL + 'posts/changeTrendingOrder',
                    data: { id: id, order_number: orderNumber },
                    success: function (data) {
                        if (data.Response === '2000') {
                            $currentElem.closest('td').data('order-number', orderNumber);
                            dataTable = rebuildTable($datatable, dataTable);
                        }
                    }
                });
            });

            initChangePostStatus('#datatable', '.post-status-button');
        });


        $('table[data-form="deleteForm"]').on('click', '.form-delete', function (e) {
            e.preventDefault();
            var $form = $(this);
            $('#confirm').modal({ backdrop: 'static', keyboard: false })
                .on('click', '#delete-btn', function () {
                    $form.submit();
                });
        });
    </script>
@endsection

@section('content')


    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Trending List
                        <small></small>
                    </h3>
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
                            <h2>Trending<small></small></h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Image</th>
                                    <th>Publisher</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Start - End Date</th>
                                    <th>Published Date</th>
                                    <th>Post Type</th>
                                    <th data-col>Order Number</th>
                                </tr>
                                </thead>


                                <tbody>

                                {{--*/ $i = 1 /*--}}
                                @foreach( $posts as $record )



                                    <tr class="" id="trending_{{$record->id}}">

                                        <td>{{ $i }}</td>

                                        <td><a onclick="showImage()" class="group1" href="{{$record->post_image}}"
                                               title={{ $record->title }}>
                                                <img style="width:70px" src="{{$record->post_image}}"/></a></td>


                                        <td>{{ $record->publisher->first_name.' '.$record->publisher->last_name }}</td>
                                        <td>{{ $record->title }}</td>
                                        {{--<td>--}}
                                        {{--@if ($record->posting_type  == 'user') --}}{{--*/ $postingTypeClass = 'btn-success'; $postingType = 'User Posted' /*--}}
                                        {{--@elseif ($record->posting_type  == 'admin') --}}{{--*/ $postingTypeClass = 'btn-danger' ; $postingType = 'Breaking News' /*--}}
                                        {{--@endif--}}

                                        {{--{{ $postingType }} </td>--}}

                                        <td id="product_status_{{$record->id}}">
                                            @if ($record->status == 'approved') {{--*/ $statusClass = 'btn-success'; $statusText = 'Published' /*--}}
                                            @elseif ($record->status == 'pending') {{--*/ $statusClass = 'btn-danger' ; $statusText = 'Draft' /*--}}
                                            @elseif ($record->status == 'scheduled' && $record->published_date >= $currentDatetime) {{--*/ $statusClass = 'btn-warning' ; $statusText = 'Scheduled' /*--}}
                                            @elseif ($record->status == 'scheduled' && $record->published_date < $currentDatetime) {{--*/ $statusClass = 'btn-success' ; $statusText = 'Published/Scheduled' /*--}}
                                            @endif
                                            <a
                                                    data-id="{{ $record->id }}"
                                                    data-status="{{ $record->status }}"
                                                    data-published_date="{{ $record->published_date }}"
                                                    data-toggle="modal"
                                                    data-target=".bs-example-modal-sm"
                                                    class="btn btn-xs post-status-button {{$statusClass}}"
                                            >
                                                @if ($statusText == 'Published/Scheduled')
                                                    Published<br>Scheduled
                                                @else
                                                    {{ $statusText }}
                                                @endif
                                            </a>
                                        </td>


                                        <td>{{ $record->start_date . ' - ' . $record->end_date }}</td>
                                        <td>{{ $record->published_date ? $record->published_date : $record->created_at }}</td>
                                        <td>{{ $record->post_type }}</td>
                                        <td data-order-number="{{$record->order_number}}">
                                            <div contenteditable="true" class="editabled" data-id="{{$record->id}}"
                                                 data-order="{{ $record->order_number }}">
                                                {{ $record->order_number }}
                                            </div>
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