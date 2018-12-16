@extends( 'frontend.layouts.web' )
@section('title', 'Broadway Event ')
@section('content')
    <?php  //dd($posts);

    if($posts) {

        $title          = isset($posts->title)         ? $posts->title         : 'N/A';
        $description    = isset($posts->description)   ? $posts->description   : 'N/A';
        $post_image     = isset($posts->post_image)         ? $posts->post_image         : 'N/A';
        $time_ago       = isset($posts->time_ago)         ? $posts->time_ago         : 'N/A';

        $start_date     =   isset($posts->start_date)         ? $posts->start_date         : 'N/A';
        $start_date     =   date('y m d',strtotime($start_date));
        $start_time     =   isset($posts->start_time)         ? $posts->start_time         : 'N/A';
        $end_time       =   isset($posts->end_time)         ? $posts->end_time         : 'N/A';
        $is_all_day     =   isset($posts->is_all_day)         ? $posts->is_all_day         : '0';

        $firstname      = isset($posts->publisher['first_name'])         ? $posts->publisher['first_name']         : 'N/A';
        $lastname       = isset($posts->publisher['last_name'])         ? $posts->publisher['last_name']         : 'N/A';

        $profile_image  = isset($posts->publisher['profile_image'])         ? $posts->publisher['profile_image']         : 'N/A';

        $publisher      = $firstname.' '.$lastname;
        $country        = isset($posts->publisher['country'])      ? $posts->publisher['country']         : '';
        $city           = isset($posts->publisher['city'])         ? $posts->publisher['city']         : '';

        if(!empty($city))  $location = $city;
        if(!empty($location))  $location = $location . ', ' .$country;
    }

    ?>
    <section class="content mainSection">
        <div class="container">
            <div class="row">
                <div class="broadWay">
                    <div class="banner">
                        <ul>
                            <li><span><img src="{{$profile_image}}"></span></li>
                            <li><h4> {{ $publisher }} <span> {{ $location }} </span></h4></li>
                        </ul>
                    <span>
                        <img src="{{ $post_image }}">
                    </span>
                        {{--<var><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $time_ago }}</var>--}}
                        <var>
                            <ul>
                                <li><a href="#"><i class="fa fa-calendar" aria-hidden="true"></i> {{$start_date}}</a></li>
                                <li><a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $start_time }} - {{ $end_time }}</a></li>
                              <?php if($is_all_day == 1) { ?>  <li><a href="#">All Day</a></li> <?php } ?>
                            </ul>
                        </var>

                    </div>

                    <kbd>
                        <h4>Aliquam fermentum neque
                            <span><i class="fa fa-map-marker" aria-hidden="true"></i> 123, Pickfords Wharf, Clink St, London SE1 9DG, UK </span>
                        </h4>

                    </kbd>
                    <div class="place">
                        <h4>{{ $title }}</h4>
                        {{ $description }}

                        <span><img src="{{ URL::to('/') }}/public/frontend/images/googleplay.png"></span>
                        {{--<ul>--}}
                        {{--<li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>--}}
                        {{--<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>--}}
                        {{--<li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>--}}
                        {{--<li><a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>--}}
                        {{--</ul>--}}

                        <a style="background: #1b5ac3; padding: 10px 20px;" onclick="check()">Open in App</a>
                    </div>

                </div>

                <div class="copyRight">© 2017 <b>Broadway Connected.</b> All right reserved</div>

            </div>
        </div>
    </section>

@endsection


@yield('JSLibraries')

<script>

    function check(){

//        setTimeout(function () { window.location = "https://itunes.apple.com/"; }, 1000);
//        window.location = "broadwayconnected://";

        var now = new Date().valueOf();
        setTimeout(function () {
            if (new Date().valueOf() - now > 100) return;
            window.location = "https://itunes.apple.com";
        }, 50);
        window.location = "broadwayconnected://";
    }

</script>

@yield('inlineJS')