<!DOCTYPE html>
<html lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# broadwayconnected-com: http://ogp.me/ns/fb/broadwayconnected-com#">
<?php
   $id = $title = $description = $post_image = $share_url = $post_type = '';


    if($posts) {

        $id          = isset($posts->id)              ? $posts->id         : '0';
        $title          = isset($posts->title)              ? $posts->title         : 'N/A';
        $description    = isset($posts->description)        ? $posts->description   : 'N/A';
        $post_image     = isset($posts->post_image)         ? $posts->post_image         : 'N/A';
        $share_url      = isset($posts->share_url)          ? $posts->share_url         : 'N/A';
        $post_type      = isset($posts->post_type)          ? $posts->post_type         : '';


    }
 ?>





        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">


        <link rel="canonical" href="{{$share_url}}">
        <meta name="title" content="{{$title}}">
        <meta name="referrer" content="unsafe-url">
        <meta name="description" content="{{$description}}">
        <meta name="theme-color" content="#000000">
        <meta property="og:title" content="{{$title}}">
        <meta property="og:url" content="{{$share_url}}">
        <meta property="og:image" content="{{$post_image}}">
        <meta property="fb:app_id" content="978323455642804">
        <meta property="og:description" content="{{$description}}">
        <meta name="twitter:description" content="{{$description}}">
        <meta name="twitter:image:src" content="{{$post_image}}">
        <meta property="author" content="Broadway Connected">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="robots" content="index, follow">
        <meta name="twitter:creator" content="@broadwayconnected">
        <meta name="twitter:site" content="@broadwayconnected">
        <meta property="og:site_name" content="Broadway Connected">
        <meta name="twitter:app:name:iphone" content="Broadway Connected">
        <meta name="twitter:app🆔iphone" content="1300230838">
        <meta name="twitter:app:url:iphone" content="broadwayconnected://{{$post_type}}/{{$id}}">
        <meta property="al:ios:app_name" content="Broadway Connected">
        <meta property="al:ios:app_store_id" content="1300230838">

        <meta property="al:ios:url" content="broadwayconnected://{{$post_type}}/{{$id}}">
        <meta property="al:web:url" content="{{$share_url}}">







    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--<meta name="description" content="{{$description}}">--}}
    {{--<meta name="author" content="Broadway Connected">--}}

    {{--<meta name="twitter:card" content="summary" />--}}
    {{--<meta name="twitter:site" content="@broadwayconnected" />--}}
    {{--<meta name="twitter:creator" content="@broadwayconnected" />--}}

    {{--<meta property="al:ios:app_store_id" content="1300230838" />--}}
    {{--<meta property="al:ios:url" content="broadwayconnected://" />--}}
    {{--<meta property="al:ios:app_name" content="Broadway Connected" />--}}


    {{--<meta property="og:title" content="{{$title}}" />--}}
    {{--<meta property="og:type" content="article" />--}}
    {{--<meta property="og:url" content="{{$share_url}}" />--}}
    {{--<meta property="og:image" content="{{$post_image}}" />--}}
    {{--<meta property="og:description" content="{{$description}}" />--}}


    <title>Broadway Connected :: @yield('title')</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:200,200i,300,300i,400,400i,500,600,|Roboto+Condensed:300,300i,400,400i,700|Roboto:100,100i,300,300i,400,400i,500,700,900" rel="stylesheet">
    <link href="{{ front_asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ front_asset('css/default.css') }}" rel="stylesheet">

@yield('CSSLibraries')

</head>

  <body class="nav-md">

  <header class="header">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <a href="#"><img src="{{ URL::to('/') }}/public/frontend/images/logo.png"></a>
              </div>
          </div>
      </div>
  </header>



    @yield('content')

    <script src="{{ front_asset('js/jquery-3.2.1.min.js') }}"></script>
    {{--<script src="{{ front_asset('js/bootstrap.js') }}"></script>--}}
    {{--<script src="{{ front_asset('js/library.js') }}"></script>--}}
    {{--<script src="{{ front_asset('js/script.js') }}"></script>--}}

    @yield('JSLibraries')

    <!-- Custom Theme JavaScript -->
    {{--<script src="{{ backend_asset('js/custom.min.js')}}"></script>--}}

@yield('inlineJS')



</body>

</html>





