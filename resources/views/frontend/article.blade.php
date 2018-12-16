@extends( 'frontend.layouts.web' )
@section('title', 'Broadway Article ')
@section('content')
    <?php
//    dd($posts);

    if ($posts) {

        $id = isset($posts->id) ? $posts->id : '0';
        $title = isset($posts->title) ? $posts->title : 'N/A';
        $description = isset($posts->description) ? $posts->description : 'N/A';
        $post_image = isset($posts->post_image) ? $posts->post_image : 'N/A';
        $time_ago = isset($posts->time_ago) ? $posts->time_ago : 'N/A';
        $post_type = isset($posts->post_type) ? $posts->post_type : '';

        $firstname = isset($posts->publisher['first_name']) ? $posts->publisher['first_name'] : 'N/A';
        $lastname = isset($posts->publisher['last_name']) ? $posts->publisher['last_name'] : 'N/A';

        $profile_image = isset($posts->publisher['profile_image']) ? $posts->publisher['profile_image'] : 'N/A';


    $publisher      = $posts->posting_type=='admin'?'Broadway Connected':$firstname.' '.$lastname;
    $country        = isset($posts->publisher['country'])      ? $posts->publisher['country']         : '';
    $city           = isset($posts->publisher['city'])         ? $posts->publisher['city']         : '';


        if (!empty($city)) $location = $city;
        if (!empty($location)) $location = $location . ', ' . $country;
        if (empty($location)) $location = 'N/A';
        $link_text = empty($posts->link_text) ? 'Show more' : $posts->link_text;
    }

    ?>
    <section class="content mainSection">
        <div class="container">
            <div class="row">
                <div class="broadWay">
                    <div class="banner">
                        <ul>
                            <li><span><img src="{{$posts->posting_type=='admin'?'/public/images/pOIIqch0mgPP.jpg':$profile_image}}"></span></li>

                            <li><h4> {{ $publisher }} <span> {{ $location }} </span></h4></li>
                        </ul>
                        <span>
                        <img src="{{ $post_image }}">
                    </span>
                        <var><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $time_ago }}</var>
                    </div>

                    <div class="place">
                        <h4>{{ $title }}</h4>
                        <p> <?php  printf(nl2br($description)); ?></p>
                        <a target="_blank" href="{{$posts->source_url}}">{{$link_text}}</a>
                        <div class="applinK">

                            <ul>
                                <li><a target="_blank"
                                       href="https://itunes.apple.com/us/app/broadway-connected/id1300230838">
                                        <img src="{{ URL::to('/') }}/public/frontend/images/appstore.png"></a></li>

                                <li><a href="#"><img onclick="check()"
                                                     src="{{ URL::to('/') }}/public/frontend/images/OpenINApp.png"></a>
                                </li>
                            </ul>


                        </div>

                    </div>

                </div>

                <div class="copyRight"><a style="color: #1b5ac3" target="_blank"
                                          href="http://www.BroadwayConnected.com"> www.BroadwayConnected.com </a>
                    <p> © 2017 Broadway Connected Inc. All rights reserved.</p></div>
            </div>
        </div>
    </section>

@endsection


@yield('JSLibraries')

<script>

    function check() {

//        setTimeout(function () { window.location = "https://itunes.apple.com/"; }, 1000);
//        window.location = "broadwayconnected://";

        var now = new Date().valueOf();
        setTimeout(function () {
            if (new Date().valueOf() - now > 100) return;
            window.location = "https://itunes.apple.com/us/app/broadway-connected/id1300230838";
        }, 50);
        window.location = "broadwayconnected://{{$post_type}}/{{$id}}";
    }

</script>

@yield('inlineJS')
