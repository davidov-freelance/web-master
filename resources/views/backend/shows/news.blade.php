@extends( 'backend.layouts.app' )
@section('title', 'News')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">

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

        $(function () {
            $('#datatable').dataTable();
            $(".group1").colorbox({ height: "75%" });
            initChangePostStatus('#datatable', '.post-status-button');
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
                    <h3>News of Shows <small></small></h3>
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
                            <h2>News <small></small></h2>
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
                                    <th>Posted As</th>
                                    <th>Status</th>
                                    <th>Published Date</th>
                                    <th>In Trending</th>
                                </tr>
                                </thead>

                                <tbody>

                                {{--*/ $i = 1 /*--}}
                                @foreach( $posts as $record )
                                    <tr class="">
                                        <td>{{ $i }}</td>
                                        <td>
                                            <a onclick="showImage()" class="group1" href="{{$record->post_image}}" title={{ $record->title }}>
                                                <img style="width:70px" src="{{$record->post_image}}" />
                                            </a>
                                        </td>
                                        <td>{{ $record->publisher->first_name.' '.$record->publisher->last_name }}</td>
                                        <td>{{ $record->title }} </td>
                                        <td>
                                            @if ($record->posting_type  == 'user') {{--*/ $postingTypeClass = 'btn-success'; $postingType = 'User' /*--}}
                                            @elseif ($record->posting_type  == 'admin') {{--*/ $postingTypeClass = 'btn-danger' ; $postingType = 'Broadway Connected' /*--}}
                                            @endif

                                            {{ $postingType }}
                                        </td>
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
                                        <td>{{ $record->published_date ? $record->published_date : $record->created_at }}</td>
                                        <td id="in_trending_{{$record->id}}">
                                            @if ($record->in_trending) {{--*/ $inTrendingClass = 'btn-success'; $statusText = 'Yes' /*--}}
                                            @elseif (!$record->in_trending) {{--*/ $inTrendingClass = 'btn-danger' ; $statusText = 'No' /*--}}
                                            @endif
                                            <a onclick="changeInTrending('{{ $record->id }}')" data-in_trending="{{ $record->in_trending }}" data-toggle="modal" data-target=".bs-example-modal-sm" class="btn {{$inTrendingClass}} btn-xs edit" > {{ $statusText }}</a>
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