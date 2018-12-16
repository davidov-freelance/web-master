@extends( 'frontend.layouts.web' )
@section('title', 'Broadway Article ')
@section('content')


    <?php  //dd($posts);

    if ($posts) {

        $id = isset($posts->id) ? $posts->id : '0';
        $title = isset($posts->title) ? $posts->title : 'N/A';
        $description = isset($posts->description) ? $posts->description : 'N/A';
        $post_image = isset($posts->post_image) ? $posts->post_image : 'N/A';
        $time_ago = isset($posts->time_ago) ? $posts->time_ago : 'N/A';
        $post_type = isset($posts->post_type) ? $posts->post_type : '';

        $start_date = isset($posts->start_date) ? $posts->start_date : 'N/A';
        $start_date = date('M d, Y', strtotime($start_date));
        $start_time = isset($posts->start_time) ? $posts->start_time : '';
        $end_time = isset($posts->end_time) ? $posts->end_time : '';

        if (!empty($start_time)) {
            $start_time = date('h:i A', strtotime($start_time));
        }
        if (!empty($end_time)) {
            $end_time = date('h:i A', strtotime($end_time));
        }

        $is_all_day = isset($posts->is_all_day) ? $posts->is_all_day : '0';
        $location = isset($posts->location) ? $posts->location : '0';

        $firstname = isset($posts->publisher['first_name']) ? $posts->publisher['first_name'] : 'N/A';
        $lastname = isset($posts->publisher['last_name']) ? $posts->publisher['last_name'] : 'N/A';

        $profile_image = isset($posts->publisher['profile_image']) ? $posts->publisher['profile_image'] : 'N/A';

        $publisher = $posts->posting_type=='admin' ? 'Broadway Connected' : $firstname . ' ' . $lastname;
        $country = isset($posts->publisher['country']) ? $posts->publisher['country'] : '';
        $city = isset($posts->publisher['city']) ? $posts->publisher['city'] : '';

        if (!empty($city)) $location = $city;
        if (!empty($location)) $location = $location . ', ' . $country;
        if (empty($location)) $location = 'N/A';
        
    }

    ?>

    <section class="content mainSection event">
        <div class="container">
            <div class="row">
                <div class="broadWay">
                    <div class="banner">
                        <ul>
                            <li>
                                <span><img src="{{$posts->posting_type=='admin'?'/public/images/pOIIqch0mgPP.jpg':$profile_image}}"></span>
                            </li>
                            <li><h4> {{ $publisher }} <span> {{ $location }} </span></h4></li>
                        </ul>
                        <span>
                        <img src="{{ $post_image }}">
                    </span>
                        <var>
                            <ul>
                                <li><a href="#"><i class="fa fa-calendar" aria-hidden="true"></i> {{$start_date}}</a>
                                </li>
                                <li><a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i> {{$start_time}}
                                        - {{$end_time}}</a></li>
                                <li><a href="#">All Day</a></li>
                            </ul>
                        </var>
                    </div>
                    <kbd>
                        <h4>
                            <span><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $location }} </span>
                        </h4>

                    </kbd>

                    <div class="place">
                        <h4>{{ $title }}</h4>
                        <p> <?php  printf(nl2br($description)); ?></p>
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

        var now = new Date().valueOf();
        setTimeout(function () {
            if (new Date().valueOf() - now > 100) return;
            window.location = "https://itunes.apple.com/us/app/broadway-connected/id1300230838";
        }, 80);
        window.location = "broadwayconnected://{{$post_type}}/{{$id}}";
    }

</script>

@yield('inlineJS')


