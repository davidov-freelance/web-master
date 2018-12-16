<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Backend\ContactUsController;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\Setting;
use App\User;
use App\Post;
use App\Tag;
use App\Category;
use App\ContactusQueries;

use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Http\Requests\Frontend\EditProfileRequest;
use App\Helpers\RESTAPIHelper;

use Validator;
use Illuminate\Support\Str;

class StatisticsController extends ApiBaseController {


//public function register(UserRegisterRequest $request)
    public function getRegisterationStats()
    {


        $registrationStats['total_users']                   = User::where('role_id', User::ROLE_MEMBER)->count();
        $registrationStats['total_active_users']            = User::where('role_id', User::ROLE_MEMBER)->where('status', User::STATUS_ACTIVE)->count();
        $registrationStats['total_blocked_users']           = User::where('role_id', User::ROLE_MEMBER)->where('status', User::STATUS_BLOCKED)->count();
        $registrationStats['total_android_users']           = User::where('role_id', User::ROLE_MEMBER)->where('device_type', User::DEVICE_TYPE_ANDROID)->count();
        $registrationStats['total_android_users_percentage']= floor(($registrationStats['total_android_users']/$registrationStats['total_users'])*100);
        $registrationStats['total_ios_users']               = User::where('role_id', User::ROLE_MEMBER)->where('device_type', User::DEVICE_TYPE_IOS)->count();
        $registrationStats['total_ios_users_percentage']    = floor(($registrationStats['total_ios_users']/$registrationStats['total_users'])*100);
        $registrationStats['total_web_users']               = User::where('role_id', User::ROLE_MEMBER)->where('device_type', User::DEVICE_TYPE_WEB)->count();
        $registrationStats['total_web_users_percentage']    = floor(($registrationStats['total_web_users']/$registrationStats['total_users'])*100);
        $registrationStats['total_fb_users']                = User::where('role_id', User::ROLE_MEMBER)->where('social_media_platform', User::SOCIALMEDIA_PLATFORM_FB)->count();

        // Application Specifice
        $registrationStats['total_tag']                     = Tag::count();
        $registrationStats['total_categories']              = Category::count();
        $registrationStats['total_articles']                = Post::where('post_type',POST::POST_TYPE_ARTICLE)->count();
        $registrationStats['total_events']                  = Post::where('post_type',POST::POST_TYPE_EVENT)->count();

        $registrationStats['total_sub_admins']              = User::where('role_id', User::ROLE_ADMIN)->where('admin_role','sub')->count();
        $registrationStats['total_moderator']               = User::where('role_id', User::ROLE_ADMIN)->where('admin_role','moderator')->count();

        return RESTAPIHelper::response($registrationStats);
    }


    public function getStats(Request $request)
    {
        $input             = $request->all();

        $from              = isset($input['start_date']) ? $input['start_date'] : date('y-m-d');
        $to                = isset($input['end_date']) ? $input['end_date'] : date('y-m-d');
        // $to                = strtotime($to,date('y-m-d 23:59:59'));

        $articlePercentage = $eventPercentage = $rejOrdersPercentage ='';
        $andUserPer = $iosUserPer = '';

        $condition['created_at'] = array($from, $to);

        $orderObj       =  Post::whereBetween('created_at',array($from, $to));

        $tPosts         = Post::whereBetween('created_at',array($from, $to))->count();
        $tArticles      = Post::whereBetween('created_at',array($from, $to))->where('post_type',Post::POST_TYPE_ARTICLE)->count();
        $tEvents        = Post::whereBetween('created_at',array($from, $to))->where('post_type',POST::POST_TYPE_EVENT)->count();
        $tFeedbacks     = ContactusQueries::whereBetween('created_at',array($from, $to))->count();

        $totalUser      = User::whereBetween('created_at',array($from, $to))->where('role_id', User::ROLE_MEMBER)->count();
        $andUser        = User::whereBetween('created_at',array($from, $to))->where('role_id', User::ROLE_MEMBER)->where('device_type', User::DEVICE_TYPE_ANDROID)->count();
        $iosUser        = User::whereBetween('created_at',array($from, $to))->where('role_id', User::ROLE_MEMBER)->where('device_type', User::DEVICE_TYPE_IOS)->count();
        $fbUser         = User::whereBetween('created_at',array($from, $to))->where('role_id', User::ROLE_MEMBER)->where('social_media_platform', User::SOCIALMEDIA_PLATFORM_FB)->count();

        if($tPosts > 0 ) {
            $andUserPer         = floor(($tArticles/$tPosts)*100);
            $iosUserPer         = floor(($tEvents/$tPosts)*100);
        }

        if(!is_null($tPosts) && $tPosts > 0 ) {

            $articlePercentage = ($tArticles/ $tPosts)*100;
            $eventPercentage = ($tEvents/ $tPosts)*100;
        }

        // Application Specific
        $registrationStats['total_posts']                   = is_null($tPosts) ? 0 : $tPosts ;
        $registrationStats['total_articles']                = is_null($tArticles) ? 0 : $tArticles ;
        $registrationStats['total_events']                  = is_null($tEvents) ? 0 : $tEvents ;
        $registrationStats['feedbacks']                     = is_null($tFeedbacks) ? 0 : $tFeedbacks ;

        $registrationStats['article_percentage']   = $articlePercentage ;
        $registrationStats['event_percentage']   = $eventPercentage ;

        $registrationStats['total_user']                   = is_null($totalUser) ? 0 : $totalUser ;
        $registrationStats['and_user_per']                 = is_null($andUserPer) ? 0 : $andUserPer ;
        $registrationStats['ios_user_per']                 = is_null($iosUserPer) ? 0 : $iosUserPer ;
        $registrationStats['fb_users']                  = is_null($fbUser) ? 0 : $fbUser ;


        return RESTAPIHelper::response($registrationStats);
    }

}