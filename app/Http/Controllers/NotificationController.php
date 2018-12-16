<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\Setting;
use App\User;
use App\Tag;
use App\Notification;
use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Http\Requests\Frontend\EditProfileRequest;
use App\Helpers\RESTAPIHelper;
use App\TagsNotificationSubscribers;

use Validator;
use App\Http\Requests\Frontend\UserRegisterRequest2;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class NotificationController extends ApiBaseController {

    public function init()
    {
        return RESTAPIHelper::response([
            'tutorial_video' => Setting::extract('app.link.tutorial_video', ''),
        ]);
    }


     //User Specific Notification, list all Notis of Specific User; All whether read or unread
     public function notifications(Request $request)
     {
         $responseArray         =   array();
         $emptyObj              =   new \stdClass();

         $offset = $request->input('offset');
         $offset = isset($offset) ? $offset : 0;


         $limit = $request->input('limit');
         $limit = isset($limit) ? $limit : 1000;

         $user_id = $request->input('user_id');
         $user_id = isset($user_id) ? $user_id : 0;
         
         if( empty($user_id) ) {return RESTAPIHelper::response($emptyObj,'Error','user id is required', false); }


         $conditions['receiver_id'] = $user_id;

         $totalRecords  = Notification::where($conditions)->count();

         $notificationObj = Notification::with(['receiverInfo','senderInfo'])->where($conditions)
                         ->orderBy('created_at', 'desc')
                         ->offset($offset)
                         ->limit($limit)
                         ->get();

        $responseArray['Notifications'] = $notificationObj;
        $responseArray['total_records'] = $totalRecords;
        return RESTAPIHelper::response($responseArray,'Success', 'Record retrieve successfully');
     }


    public function delete(Request $request)
    {
        $responseArray         =   array();
        $emptyObj              =   new \stdClass();

        $user_id               = $request->input('user_id');
        $user_id               = isset($user_id) ? $user_id : 0;

        $notification_id       = $request->input('notification_id');
        $notification_id       = isset($notification_id) ? $notification_id : 0;

        if( empty($user_id) ) {return RESTAPIHelper::response($emptyObj,'Error','user id is required', false); }
        if( empty($notification_id) ) {return RESTAPIHelper::response($emptyObj,'Error','notification id is required', false); }

        $notification = Notification::find($notification_id);
        $notification->delete();

        return RESTAPIHelper::response($emptyObj , 'Success', 'Notification has been deleted successfully');

    }


    public function NotificationMarkAsRead(Request $request)
    {
        $responseArray         = array();
        $emptyObj              = new \stdClass();

        $user_id               = $request->input('user_id');
        $user_id               = isset($user_id) ? $user_id : 0;

        $notification_id       = $request->input('notification_id');
        $notification_id       = isset($notification_id) ? $notification_id : 0;

        $action_id             = $request->input('action_id');
        $action_id             = isset($action_id) ? $action_id : 0;

        if( empty($user_id) ) { return RESTAPIHelper::response($emptyObj , 'Error','user id is required', false); }

        if( empty($notification_id) && empty($action_id) ) { return RESTAPIHelper::response($emptyObj , 'Error','notification id or action id is required', false); }


       if($notification_id > 0 ) {

           $notification           = Notification::find($notification_id);
       } else if ($action_id > 0 ) {

           $notification           = Notification::where('action_id',$action_id)->where('receiver_id',$user_id)->first();
       }


        if($notification){

            $updateData['is_read'] = 1;
            $notification->update($updateData);

            $unreadCount = Notification::where('receiver_id',$user_id)->where('is_read',0)->count();

            $responseArray['unread_count'] = $unreadCount;

            return RESTAPIHelper::response($responseArray,'Success', 'Notification has been marked as read');

        } else {

            return RESTAPIHelper::response($emptyObj , 'Error','wrong notification id is required', false);
        }

    }

    public function unreadNotificationCount(Request $request)
    {
        $responseArray = array();
        $emptyObj = new \stdClass();

        $user_id = $request->input('user_id');
        $user_id = isset($user_id) ? $user_id : 0;


        if (empty($user_id)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user id is required', false);
        }


        $unreadCount = Notification::where('receiver_id', $user_id)->where('is_read', 0)->count();

        $responseArray['unread_count'] = $unreadCount;

        return RESTAPIHelper::response($responseArray, 'Success', 'record retrieve successfully');

    }

    public function subscribeToTag(Request $request) {
        $userId = (int) $request->input('user_id');
        $tagId = (int) $request->input('tag_id');

        if (!$userId) {
            return RESTAPIHelper::response('', 'Error', 'User id is required');
        }

        if (!$tagId) {
            return RESTAPIHelper::response('', 'Error', 'Tag id is required');
        }

        $condition = [
            'user_id' => $userId,
            'tag_id' => $tagId,
        ];

        $data = TagsNotificationSubscribers::where($condition)->first();

        if ($data) {
            return RESTAPIHelper::response('', 'Error', 'You are already subscribed to this topic before');
        }

        TagsNotificationSubscribers::create(['user_id' => $userId, 'tag_id' => $tagId]);
        return RESTAPIHelper::response('', 'Success', 'You has been subscribed successfully on this tag');
    }

    public function getNotificationTags(Request $request) {
        $keyword =  isset($request['keyword']) ? $request['keyword'] : NULL ;

        $tagsObj = Tag::select('tags.*')->rightJoin('tags_notification_subscribers', 'tags_notification_subscribers.tag_id', '=', 'tags.id');

        if ($keyword) {
            $tagsObj = $tagsObj->where('tag', 'LIKE', "%$keyword%");
        }
        $tags = $tagsObj->groupBy('tags.id')->get();

        return RESTAPIHelper::response($tags);
    }

}
