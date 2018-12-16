@extends( 'backend.layouts.app' )

@section('title', 'Article Details')

<style>
    .main-image, .thumb-image {
        margin: 0;
        padding: 0;
        float: left;
        width: 100%;
    }

    .main-image li {
        list-style: none;
        padding: 0;
        border-radius: 5px;
        overflow: hidden;
    }

    .main-image li img {
        width: 100%;
    }

    .thumb-image li {
        list-style: none;
        padding: 0;
        border: 1px solid #ccc;
        margin: 10px 11.8px 0px 0;
        width: 30%;
    }

    .thumb-image li:nth-child(3n) {
        margin-right: 0px;
    }
</style>

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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCg5miLydu02jSxoHssYp5IEcnKzRl3Itk&callback="></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        $(function () {
            $(".group1").colorbox({ rel: 'group1', height: "75%" });
            initDeleteForm('#myTabContent', '.form-delete');
            myMap();
        });

        function myMap() {
            var mapContainer = document.getElementById("googleMap");

            if (!mapContainer) {
                return;
            }

            var myLatLng = { lat: 24.958198, lng: 67.068800 };

            var mapProp = {
                center: new google.maps.LatLng(24.958198, 67.068800),
                zoom: 5,
            };

            var map = new google.maps.Map(mapContainer, mapProp);

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: 'Hello World!'
            });
        }
    </script>
@endsection

@section('content')
    @include('backend.layouts.modal')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3> Article Detail</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>{{ $post['title'] or "N/A"}}</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="col-md-3 col-sm-3 col-xs-12 profile_left"
                                 style="border:1px solid #ddd; height: -webkit-fill-available">
                                <h3> PUBLISHER</h3>
                                <div class="profile_img">
                                    <div id="crop-avatar">
                                        <img class="img-responsive avatar-view"
                                             src="{{$post->publisher->profile_image}} " alt="">
                                    </div>
                                    <br>
                                </div>
                                <ul class="list-unstyled user_data">
                                    <li>
                                        <label>Name :</label>
                                        {{ $post->publisher->first_name . ' ' . $post->publisher->last_name }}
                                    </li>
                                    <li><label>Email : </label> {{ $post->publisher->email }}</li>
                                    <li><label>Handle :</label>{{ $post->publisher->handle }}</li>
                                    <li><label>Position :</label>{{ $post->publisher->headline_position }}</li>
                                    <li>
                                        <label>Featured User :</label>
                                        {{ $post->publisher->is_featured == '1' ? 'Yes' : 'No' }}
                                    </li>
                                </ul>
                                <br/>
                            </div>

                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <h3> ARTICLE</h3>
                                <div class="profile_img">
                                    <div id="crop-avatar">
                                        <img style="max-height: 300px;" class="img-responsive avatar-view"
                                             src="{{$post->post_image}} " alt="">
                                    </div>
                                    <br>
                                </div>
                                <ul class="list-unstyled user_data">
                                    <li><label>Title :</label> {{ $post->title }}</li>
                                    <li><label>Description : </label> {{ $post->description }}</li>
                                    <li><label>Likes Count : </label> {{ $post->likes }}</li>
                                    <li><label>Published Date : </label> {{ $post->created_at }}</li>
                                </ul>
                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#tab_content1" id="home-tab"
                                               role="tab" data-toggle="tab"
                                               aria-expanded="true">Comments</a>
                                        </li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content1"
                                             aria-labelledby="home-tab">

                                            <!-- start recent activity -->
                                            <ul class="messages">
                                                @if($post->comments)
                                                    @foreach($post->comments as $comment)
                                                        <li>
                                                            <img src="{{$comment->user['profile_image']}}"
                                                                 class="avatar"
                                                                 alt="Avatar">
                                                            <div class="message_date">
                                                                <h3 class="date text-info">
                                                                    {{Carbon\Carbon::parse($comment->created_at)->format('d')}}
                                                                </h3>
                                                                <p class="month">
                                                                    {{Carbon\Carbon::parse($comment->created_at)->format('M Y')}}
                                                                </p>

                                                            </div>
                                                            <div class="message_wrapper">
                                                                <h4 class="heading">
                                                                    {{ $comment->user['first_name'] }}
                                                                </h4>
                                                                <blockquote class="message">
                                                                    {{ $comment->comment }}
                                                                </blockquote>
                                                                {{Form::model($comment, ['method' => 'delete', 'url' => 'backend/comment/remove/' . $comment->id, 'class' => 'form-inline form-delete'])}}
                                                                    {{Form::hidden('id', $comment->id) }}
                                                                    {{Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal', 'data-toggle' => 'modal'])}}
                                                                {{Form::close()}}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                    <br>
                                                @else
                                                    <li> No Comments</li>
                                                @endif
                                            </ul>
                                            <!-- end recent activity -->
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="tab_content3"
                                             aria-labelledby="profile-tab">
                                            <p>{{$post['description'] or "N/A"}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div id="googleMaps" style="width:100%;height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->
@endsection