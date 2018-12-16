<?php
//$user =  Auth::user();
$adminUser = Auth::User();
$full_name = 'Admin';
$sub_admin = $adminId = 0;

if ($adminUser) {

    //dd($adminUser);
    $adminData = $adminUser->toArray();
    $adminId = $adminData['id'];
    $profile_picture = $adminData['profile_picture'];
    $profile_picture = $adminData['profile_image'];
    $full_name = $adminData['first_name'];
    $admin_role = $adminData['admin_role'];

}

?>


<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">

            <a class="site_title" href="{{ backend_url('dashboard') }}">BROADWAY CONNECTED</a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile">
            <div class="profile_pic">
                <img src="<?php echo $profile_picture; ?>" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $full_name; ?></h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">

                    @if ($admin_role == 'super')
                    <li><a href="{{ backend_url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>
                        <a><i class="fa fa-group fa-fw"></i> Sub Admins <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('admin') }}"> View Sub Admins</a></li>
                            <li><a href="{{ backend_url('admin/add') }}"> Add Admin</a></li>
                        </ul>
                    </li>
                    @endif

                    @if ($admin_role != 'editor')
                    <li>
                        <a><i class="fa fa-group fa-fw"></i> User <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('user') }}"> View User</a></li>
                            <li><a href="{{ backend_url('user/add') }}"> Add User</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-trophy fa-fw"></i> Badges <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('badges') }}"> View Badges</a></li>
                            <li><a href="{{ backend_url('badges/add') }}"> Add Badge</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Categories <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('categories') }}"> View Categories</a></li>
                            <li><a href="{{ backend_url('categories/add') }}"> Add Category</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Tags <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('tags') }}"> View Tags</a></li>
                            <li><a href="{{ backend_url('tags/add') }}"> Add New</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Fields Of Work <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('fields') }}"> View Fields</a></li>
                            <li><a href="{{ backend_url('fields/add') }}"> Add New</a></li>
                        </ul>
                    </li>
                    @endif

                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Articles <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('articles') }}"> View Articles</a></li>
                            <li><a href="{{ backend_url('articles/add') }}"> Add New</a></li>
                        </ul>
                    </li>


                    @if ($admin_role != 'editor')
                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Events <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('events') }}"> View Events</a></li>
                            <li><a href="{{ backend_url('events/add') }}"> Add New</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-asterisk fa-fw"></i> Trending <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('trending') }}"> View Trending</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-question-circle fa-fw"></i> Question of the Day <span
                                    class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('questions') }}"> Questions</a></li>
                            <li><a href="{{ backend_url('questions/users') }}"> Users</a></li>
                            <li><a href="{{ backend_url('questions/add') }}"> Add New</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-briefcase fa-fw"></i> Business of Broadway <span
                                    class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('shows') }}"> View Shows</a></li>
                            <li><a href="{{ backend_url('shows/theaters') }}"> Theaters</a></li>
                            <li><a href="{{ backend_url('shows/news') }}"> News</a></li>
                            <li><a href="{{ backend_url('shows/add') }}"> Add New Show</a></li>
                            <li><a href="{{ backend_url('shows/theaters/add') }}"> Add New Theater</a></li>
                        </ul>
                    </li>

                    <li><a><i class="fa fa-book"></i> Cms <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('cms') }}"> Pages List</a></li>

                        </ul>
                    </li>


                    <li><a><i class="fa fa-book"></i> FAQs <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('faq') }}"> FAQs</a></li>
                        </ul>
                    </li>

                    <li><a><i class="fa fa-volume-up"></i> Push Notification <span
                                    class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('push/send') }}"> Send Notifications</a></li>
                        </ul>
                    </li>

                    <li><a><i class="fa fa-volume-up"></i> Groups <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('groups') }}"> Listing</a></li>
                            <li><a href="{{ backend_url('group/new') }}"> Add Group</a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-comments fa-fw"></i> Contact Us <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ backend_url('contacts') }}"> Contact Us (Feedback)</a></li>
                        </ul>
                    </li>
                    @endif

                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
    {{--<div class="sidebar-footer hidden-small">--}}
    {{--<a data-toggle="tooltip" data-placement="top" title="Settings">--}}
    {{--<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>--}}
    {{--</a>--}}
    {{--<a data-toggle="tooltip" data-placement="top" title="FullScreen">--}}
    {{--<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>--}}
    {{--</a>--}}
    {{--<a data-toggle="tooltip" data-placement="top" title="Lock">--}}
    {{--<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>--}}
    {{--</a>--}}
    {{--<a data-toggle="tooltip" data-placement="top" title="Logout">--}}
    {{--<span class="glyphicon glyphicon-off" aria-hidden="true"></span>--}}
    {{--</a>--}}
    {{--</div>--}}
    <!-- /menu footer buttons -->
    </div>
</div>

<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i style="color: #fff;" class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false">
                        <img src="{{ $profile_picture }}" alt="">{{ $full_name }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">

                        <li><a href="{{ backend_url('admin/change-password/'.$adminId)  }}"><i
                                        class="fa fa-lock pull-right"></i> Changed Password</a></li>
                        <li><a href="{{ backend_url('admin/edit/'.$adminId)  }}"><i class="fa fa-lock pull-right"></i>
                                Update Profile</a></li>

                        {{--<li><a href="{{ backend_url('setting') }}"><i class="fa fa-pencil pull-right"></i> Setting</a></li>--}}

                        <li><a href="{{ backend_url('logout') }}"><i class="fa fa-sign-out pull-right"></i> Logout</a>
                        </li>
                    </ul>
                </li>


            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->

