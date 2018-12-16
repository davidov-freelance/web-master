<?php

use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Artisan;

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', function () {
   // return view('welcome');
    return redirect( 'backend/login' );
});

Route::get('/home', function () {
    dd(Auth::user());
});

Route::get('/terms', function () {
    return view('terms');
});

Route::get('post/article/{id}', 'PostsController@frontArticle');
Route::get('post/event/{id}', 'PostsController@frontEvent');

Route::group(['middleware' => ['api'], 'prefix' => 'api'], function () {
    Route::post('remove-cache', 'UserController@deleteCacheFiles');
    Route::post('push', 'UserController@testPush');
    Route::post('push-ios', 'UserController@testPushIos');
    Route::get('users', 'UserController@getUsers');
    Route::get('all-users', 'UserController@getAllUsers');
    Route::get('groups', 'UserController@getAllGroups');

    Route::get('notification/tags', 'NotificationController@getNotificationTags');

    Route::get('failure', 'ApiBaseController@FailureResponse');
    Route::get('failure/block', 'ApiBaseController@FailureResponseBlock');

    /// Admin
    Route::get('user/changeStatus/{userId}', 'UserController@changeStatus');

	Route::get('message', 'OrderController@message');

    Route::post('register', 'UserController@register');
    Route::post('signup', 'UserController@signup');
    Route::post('signin', 'UserController@signin');
    Route::post('user/verify', 'UserController@verifyAccount');
    Route::post('user/resend-sms-verification', 'UserController@resendCode');
    Route::post('updatedeviceinfo', 'UserController@updateDeviceToken');
    Route::post('settings', 'UserController@userSetting');
    Route::post('user/following', 'UserController@followUnfollow');
    Route::get('user/followings', 'UserController@getFollowings');
    Route::get('user/followers', 'UserController@getFollowers');
    Route::get('user/profile', 'UserController@getProfile');
    Route::post('changepassword', 'UserController@changePassword');

    Route::get('user/articles', 'PostsController@userArticles');
    Route::get('user/events', 'PostsController@userEvents');

    Route::get('user/discoveries', 'UserController@getDiscoverUser');
    Route::get('user/suggestion', 'UserController@getSuggestedUser');

    /// cms pages ....
    Route::get('cms', 'CmsController@getCmsPage');
    Route::get('faq', 'FaqsController@getHelpContent');
    // Contact Us Queries ........
    Route::post('contact-us', 'ContactusQueriesController@_create');

    // Report Post  ........
    Route::post('report/post', 'ReportPostController@_create');
    Route::get('report/option', 'ReportPostController@getReportOption');

    Route::get('tags', 'TagsController@getTags');
    Route::get('fields', 'FieldsController@getFields');

    Route::post('article', 'PostsController@create');
    Route::post('edit-article', 'PostsController@updateArticle');
    Route::post('remove-post', 'PostsController@deletePost');

    Route::post('event', 'PostsController@createEvent');
    Route::post('edit-event', 'PostsController@updateEvent');
    Route::post('add-to-calendar', 'PostsController@addToCalendar');

    Route::post('post/comment', 'PostsController@insertComment');
    Route::get('post/comments', 'PostsController@retrieveComments');

    Route::get('posts', 'PostsController@retrieve');
    Route::get('articles', 'PostsController@retrieve');
    Route::get('trending', 'PostsController@trending');
    Route::get('calender', 'PostsController@getCalenderEvent');
    Route::get('calender/detail', 'PostsController@getSingleDateCalender');
    Route::get('post/detail', 'PostsController@postsDetail');
    Route::get('articles-by-tag', 'PostsController@articlesByTag');

    Route::get('search', 'PostsController@search');

    Route::get('post/favourites', 'PostsController@userFavouritePosts');
    Route::post('post/favourite', 'PostsController@markFavouriteUnfavourite');

    /// CHAT module STRATS HERE  ========== ....
    Route::post('conversation/message', 'ConversationsController@create');
    Route::get('conversation/thread/messages', 'ConversationsController@conversationsThreadDetail');
    Route::get('conversation/threads', 'ConversationsController@userConversations');
    /// chat module ENDS HERE  ....

    // Depreciated .........................
    Route::post('sociallogin', 'UserController@socialLogin');
    Route::post('socialLogin', 'UserController@socialLogin');

    Route::post('login', 'UserController@login');
    Route::post('forgotpassword', 'UserController@forgotPassword');
    Route::post('user/update', 'UserController@updateUser');

    Route::post('imageUpload', 'VehiclesController@imageUpload');
    Route::post('guestuser/token', 'UserController@guestUserToken');
    // Depreciated .........................

    //// Services related to order will come here ...

    //User Specific Notifications
    Route::get('notifications', 'NotificationController@notifications');
    Route::get('notification/count', 'NotificationController@unreadNotificationCount');
    Route::get('notification/mark/read', 'NotificationController@NotificationMarkAsRead');
    Route::get('notification/delete', 'NotificationController@delete');

    Route::post('subscribe-to-tag', 'NotificationController@subscribeToTag');

    Route::get('categories', 'CategoriesController@getCategories');
    Route::get('categories/all', 'CategoriesController@getAllCategories');
    Route::get('subcategories/category/{id}', 'CategoriesController@getSubCategories');

    /// Api for Admin Panel
    Route::get('stats/user', 'StatisticsController@getRegisterationStats');
    Route::get('statistics', 'StatisticsController@getStats');
    Route::post('posts/changePostStatus', 'PostsController@changeStatus');
    Route::post('user/feature', 'UserController@markUnmarkFeatured');
    Route::post('posts/repostPost', 'PostsController@repostPost');
    Route::get('posts/articles', 'PostsController@getPostsForPush');
    Route::get('posts/events', 'PostsController@getEventPostsForPush');
    
    Route::get('question/get-daily', 'QuestionsController@getQuestionOfTheDay');
    Route::post('question/send-answer', 'QuestionsController@answerQuestion');
    
    // Shows
    Route::get('shows', 'ShowsController@getShows');
    Route::get('show/details', 'ShowsController@showDetails');
    Route::get('show/grosses', 'ShowsController@getGrosses');
    Route::get('shows/news', 'ShowsController@getNews');

    /// Api for Admin Panel

    Route::group(['middleware' => 'jwt-auth'], function () {

        Route::post('logout', 'UserController@logout');

        //Mark Notis read
        Route::post('Notification/send', 'NotificationController@sendNotifications');

        //Enlist all products
        Route::get('Products', 'ProductController@getAllProducts');

        //Enlist single products
        Route::get('Products/{productId}', 'ProductController@getProduct');

        //Enlist all products of specific category
        Route::get('Products/Category/{categoryId}', 'ProductController@getProductByCategory');

        Route::group(['prefix' => 'profile'], function() {
            Route::post('me', 'ApiController@viewMyProfile');
            Route::post('update', 'ApiController@updateMyProfile');
        });
    });
});


Route::group(['middleware' => 'web', 'prefix' => 'backend', 'namespace' => 'Backend'], function () {

    Route::match(['GET', 'POST'], 'login', 'Auth\AuthController@adminLogin');
    Route::match(['GET', 'POST'], 'reset-password/{token?}', 'Auth\PasswordController@resetPasswordAction');
    Route::post('reset-password-finally', 'Auth\PasswordController@reset');

    Route::group(['middleware' => ['admin']], function () {
		Route::get('admin', 'AdminController@getIndex');
        Route::delete('admin/{admin}', 'AdminController@destroy');
        Route::get('admin/edit/{admin}', 'AdminController@edit');
        Route::put('admin/{admin}', 'AdminController@update');
        Route::get('admin/add', 'AdminController@add');
        Route::post('admin/create', 'AdminController@create');
        Route::post('admin/change-to-user', 'AdminController@changeToUser');

        Route::get('admin/change-password/{id}', 'AdminController@changePasswordForm');
        Route::put('Changed_password/update/{user}', 'AdminController@updatePassword');

		
		Route::get('product', 'ProductController@getIndex');
		
        Route::get('logout', 'Auth\AuthController@logout');
        Route::get('dashboard', 'DashboardController@getIndex');
        Route::get('setting/profile', 'SettingController@getProfileSetting');
        Route::post('setting/profile', 'SettingController@postProfileSetting');
        Route::match(['GET', 'POST'], 'setting/setting', 'SettingController@processSetting');

        Route::get('user', 'UserController@getIndex');
        Route::delete('user/{user}', 'UserController@destroy');
        Route::get('user/edit/{user}', 'UserController@edit');
        Route::put('user/{user}', 'UserController@update');
        Route::get('user/add', 'UserController@add');
        Route::post('user/create', 'UserController@create');
        Route::get('user/profile/{id}', 'UserController@profile');
        Route::get('user/changeStatus/{userId}', 'UserController@changeStatus');
        Route::post('user/change-to-admin', 'UserController@changeToAdmin');

        #Post/Products start
        Route::get('articles', 'PostsController@getIndex');
        Route::get('posts', 'PostsController@getIndex');
        Route::get('articles/add', 'PostsController@add');
        Route::get('articles/{id}', 'PostsController@getUserArticles');
        Route::get('articles/group/{id}', 'PostsController@getGroupArticles');
        Route::delete('articles/{post}', 'PostsController@destroy');
        Route::get('articles/edit/{post}', 'PostsController@edit');
        Route::put('articles/update/{post}', 'PostsController@update');

        Route::post('articles/create', 'PostsController@create');
        Route::get('articles/detail/{post}', 'PostsController@details');
        Route::delete('articles/remove/{post}', 'PostsController@destroy');
        Route::delete('comment/remove/{comment}', 'PostsController@removeComment');


        Route::get('events', 'PostsController@getEventIndex');

        Route::delete('events/{post}', 'PostsController@eventDestroy');
        Route::get('events/edit/{post}', 'PostsController@editEvent');
        Route::put('events/update/{post}', 'PostsController@updateEvent');
        Route::get('events/add', 'PostsController@addEvent');
        Route::get('events/{id}', 'PostsController@getUserEventIndex');
        Route::get('events/group/{id}', 'PostsController@getGroupEvents');
        Route::post('events/create', 'PostsController@createEvent');
        Route::get('events/detail/{post}', 'PostsController@EventDetail');
        Route::delete('events/remove/{post}', 'PostsController@destroy');
    
    
        Route::get('trending', 'PostsController@getTrending');
        
        // QUESTIONS OF THE DAY PAGES ......
        Route::get('questions', 'QuestionsController@getIndex');
        Route::get('questions/users', 'QuestionsController@getUsers');
        Route::get('questions/add', 'QuestionsController@add');
        Route::post('questions/create', 'QuestionsController@create');
        Route::get('questions/edit/{question}', 'QuestionsController@edit');
        Route::put('questions/update/{question}', 'QuestionsController@update');
        Route::delete('questions/remove/{question}', 'QuestionsController@remove');
        
        // BUSINESS BROADWAY ......
        Route::get('shows', 'ShowsController@getIndex');
        Route::get('shows/news', 'ShowsController@getNews');
        Route::get('shows/add', 'ShowsController@add');
        Route::post('shows/create', 'ShowsController@create');
        Route::get('shows/edit/{show}', 'ShowsController@edit');
        Route::put('shows/update/{show}', 'ShowsController@update');
        Route::delete('shows/remove/{show}', 'ShowsController@remove');
        Route::get('shows/gross/{show}', 'ShowsController@gross');
        Route::get('shows/gross/add/{show}', 'ShowsController@addGross');
        Route::get('shows/gross/edit/{showGross}', 'ShowsController@editGross');
        Route::put('shows/gross/update/{showGross}', 'ShowsController@updateGross');
        Route::delete('shows/gross/remove/{gross}', 'ShowsController@removeGross');
        Route::post('shows/gross/save/{show}', 'ShowsController@saveGross');
        Route::post('shows/gross/check/{show}', 'ShowsController@checkGross');
        Route::get('shows/theaters', 'ShowsController@getTheaters');
        Route::get('shows/theaters/add', 'ShowsController@addTheater');
        Route::post('shows/theaters/create', 'ShowsController@createTheater');
        Route::get('shows/theaters/edit/{theater}', 'ShowsController@editTheater');
        Route::put('shows/theaters/update/{theater}', 'ShowsController@updateTheater');
        Route::delete('shows/theaters/remove/{theater}', 'ShowsController@removeTheater');
        // AJAX for gross adding
        Route::post('shows/gross/ajax-save/{show}', 'ShowsController@ajaxSaveGross');

        // FAQ PAGES ......
        Route::get('faq', 'FaqsController@getIndex');
        Route::delete('faq/{id}', 'FaqsController@destroy');
        Route::get('faq/edit/{type}', ['uses' => 'FaqsController@edit']);
        Route::put('faq/{id}', 'FaqsController@update');
        Route::get('faq/add', 'FaqsController@add');
        Route::post('faq/update', 'FaqsController@update');

        // CMS PAges Controller ......
        Route::get('cms', 'CmsController@getIndex');
        Route::delete('cms/{page_id}', 'CmsController@destroy');
        Route::get('cms/edit/{page_id}', 'CmsController@edit');
        Route::put('cms/{page_id}', 'CmsController@update');
        Route::get('cms/add', 'CmsController@add');
        Route::post('cms/create', 'CmsController@create');

        #Badges
        Route::get('badges', 'BadgesController@getIndex');
        Route::delete('badges/{badge}', 'BadgesController@destroy');
        Route::get('badges/edit/{badge}', 'BadgesController@edit');
        Route::put('badges/update/{badge}', 'BadgesController@update');
        Route::get('badges/add', 'BadgesController@add');
        Route::post('badges/create', 'BadgesController@create');

        #Categories
        Route::get('categories', 'CategoriesController@getIndex');
        Route::delete('categories/{category}', 'CategoriesController@destroy');
        Route::get('categories/edit/{category}', 'CategoriesController@edit');
        Route::put('categories/update/{category}', 'CategoriesController@update');
        Route::get('categories/add', 'CategoriesController@add');
        Route::post('categories/create', 'CategoriesController@create');

        #tags
        Route::get('tags', 'TagsController@getIndex');
        Route::delete('tags/{tag}', 'TagsController@destroy');
        Route::get('tags/edit/{tag}', 'TagsController@edit');
        Route::put('tags/update/{tag}', 'TagsController@update');
        Route::get('tags/add', 'TagsController@add');
        Route::post('tags/create', 'TagsController@create');


        #fields
        Route::get('fields', 'FieldsController@getIndex');
        Route::delete('fields/{field}', 'FieldsController@destroy');
        Route::get('fields/edit/{field}', 'FieldsController@edit');
        Route::put('fields/update/{field}', 'FieldsController@update');
        Route::get('fields/add', 'FieldsController@add');
        Route::post('fields/create', 'FieldsController@create');


        // Push Notification PAges Controller ......

        Route::get('push/send', 'PushNotificationController@add');
        Route::post('push/sendpush', 'PushNotificationController@sendNotification');


        Route::get('contacts', 'ContactUsController@getIndex');
        Route::delete('contact/{contact}', 'ContactUsController@destroy');

        #Categories start
        Route::get('groups', 'GroupsController@getIndex');
        Route::delete('group/{group}', 'GroupsController@destroy');
        Route::get('group/edit/{group}', 'GroupsController@edit');
        Route::put('group/update/{group}', 'GroupsController@update');
        Route::get('group/new', 'GroupsController@add');
        Route::post('group/create', 'GroupsController@create');

        //Route::match(['GET', 'POST'], 'user/add', 'UserController@add');

        Route::get('report/{index?}', 'ReportController@getIndex');
        Route::delete('report/{report}', 'ReportController@destroy');
        
        /* API FOR ADMIN */
        Route::post('posts/changePostInTrending', 'PostsController@changeInTrending');
        Route::post('posts/changeTrendingOrder', 'PostsController@changeTrendingOrder');
        Route::post('posts/changePostInFeed', 'PostsController@changeInFeed');
        
        Route::post('questions/remove/{question_id}', 'QuestionsController@remove');
        Route::post('questions/create', 'QuestionsController@create');
        Route::get('questions/get-answered-users', 'QuestionsController@getAnsweredUsers');

    });
});

Route::get('/test', function() {
    $userCreated = App\User::find(1);
    EmailHelper::sendMail($userCreated->email, 'Welcome on Board - ValuationApp', 'test');
});

Route::get('/migrate', function() {
    Artisan::call('migrate', ['--force' => true]);
    exit('Migration was successful');
});


