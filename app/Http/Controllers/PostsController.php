<?php

namespace App\Http\Controllers;

use App\GroupMember;
use App\UserFollowing;
use Illuminate\Http\Request;

use Hash;
use Config;
use JWTAuth;
use App\Setting;
use App\User;
use App\Tag;
use App\Post;
use App\PostTag;
use App\PostComment;
use App\FavouritePost;
use App\Notification;
use App\UserCalendar;
use App\Helpers\RESTAPIHelper;
use Validator;
use Auth;
use App\Http\Requests\Frontend\PostCreateRequest;
use App\Http\Requests\Frontend\PostEventCreateRequest;
use App\Http\Requests\Frontend\PostEventUpdateRequest;
use App\Http\Requests\Frontend\PostUpdateRequest;
use App\Http\Requests\Frontend\AddCalendarRequest;

use App\Http\Requests\Frontend\PostCommentCreateRequest;
use Illuminate\Support\Facades\DB;

class PostsController extends ApiBaseController
{
    public function init()
    {
        return RESTAPIHelper::response([
            'tutorial_video' => Setting::extract('app.link.tutorial_video', ''),
        ]);
    }

    public function create(PostCreateRequest $request)
    {
        $postData = $request->all();
        $responseArray = array();

        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $currDate = date('Y-m-d H:i:s');
        if (empty($postData['published_date'])) {
            $postData['published_date'] = $currDate;
        }
        $postData['tags'] = isset($postData['tags']) ? $postData['tags'] : '';

//        $is_authorized = $this->checkTokenValidity($userId);
//        if ($is_authorized == 0) {
//            return RESTAPIHelper::response('', 'Error', 'Invalid Token or User Id', false);
//        }

        if ($file = $request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/articles/';
            $file->move($destinationPath, $fileName);
            $postData['image'] = $fileName;
        }

        $postObj = Post::create($postData);


        if (!empty($postData['tags'])) {
            
            $this->addTags($postObj->id, $postData['tags']);
        }

        if ($_SERVER['SERVER_NAME'] == '34.216.17.158') {
            
            $userFollowees = UserFollowing::where('followee_id', $userId)->pluck('follower_id')->toArray();

            $senderInfo                 = User::where('id', $userId)->first();

            if($userFollowees){

                foreach($userFollowees as $receiverId){

                    /// PUSH NOTIFICATION WORK ======================================================
                    $recInfo                    = User::where('id',$receiverId)->first();

                    $notiMessage = $senderInfo->first_name.' '.$senderInfo->last_name.' has published new article';
                    /// SEnding Notifications
                    $notification['receiver_id'] = $receiverId;
                    $notification['sender_id']   = $userId;
                    $notification['message']     = $notiMessage;
                    $notification['action_type'] = 'article';
                    $notification['action_id']   = $postObj->id;
                    Notification::create($notification);

                    if($recInfo) {

                        $deviceType             = $recInfo->device_type;
                        $deviceToken            = $recInfo->device_token;
                        $notification_status    = $recInfo->notification_status;

                        if(!is_null($deviceType) && !is_null($deviceToken) && ($notification_status =='1')) {

                            if($deviceType == 'android') {

//                                $postArray = array('title' => 'Broadway Connected', 'message' => $notiMessage, 'sound' => 'default');
                                // $this->SendPushNotificationAndroid($deviceToken,$postArray);
                            } else {

                                $apsArray =  array( 'alert' => $notiMessage,
                                    'action_id' => $notification['action_id'],
                                    'action_type'=>$notification['action_type'],
                                    'sound' => 'default');

                                $this->SendPushNotification($deviceToken, $apsArray , $receiverId );
                            }
                        }
                    }
                    /// PUSH NOTIFICATION WORK ======================================================
                }
            }
        }

        $postObj = Post::with(['publisher', 'tags'])->where('id', $postObj->id)->get();
        $responseArray['Posts'] = $postObj;

        return RESTAPIHelper::response($responseArray, 'Success', 'Article Published, Check it out on your newsfeed!');
    }

    public function updateArticle(PostUpdateRequest $request)
    {

        $postData = $request->all();
        $emptyObj = new \stdClass();
        $responseArray = array();
        $postId = (int) $request->input('post_id');
        
        $post = Post::find($postId);

        $postData['tags'] = isset($postData['tags']) ? $postData['tags'] : '';

        if ($post) {

            if ($file = $request->hasFile('image')) {
                
                $file = $request->file('image');

                $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
                $destinationPath = public_path() . '/images/articles/';
                $file->move($destinationPath, $fileName);
                $postData['image'] = $fileName;
            }

            $post->update($postData);
            
            if (!empty($postData['tags'])) {
                
                $this->updateTags($post->id, $postData['tags']);
            }

            $postObj = Post::with(['publisher', 'tags'])->where('id', $postId)->get();

            // formating in array ...
            $responseArray['Posts'] = $postObj;

            return RESTAPIHelper::response($responseArray, 'Success', 'Article has been updated successfully');

        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong post_id is required', false);

        }


    }

    public function createEvent(PostEventCreateRequest $request)
    {
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;
        $postData['post_type'] = POST::POST_TYPE_EVENT;
        $responseArray = array();

        $postData['tags'] = isset($postData['tags']) ? $postData['tags'] : '';

        $currDate = date('Y-m-d H:i:s');
        $postData['published_date'] = $currDate;

        if ($file = $request->hasFile('image')) {
            $file = $request->file('image');

            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/articles/';
            $file->move($destinationPath, $fileName);
            $postData['image'] = $fileName;
        }

        $postObj = Post::create($postData);

        if (!empty($postData['tags'])) {
            $this->addTags($postObj->id, $postData['tags']);
        }

        $userFollowees = UserFollowing::where('followee_id', $userId)->pluck('follower_id')->toArray();

        $senderInfo = User::where('id', $userId)->first();
        
        if ($userFollowees) {

            foreach ($userFollowees as $receiverId) {

                /// PUSH NOTIFICATION WORK ======================================================
                $recInfo = User::where('id', $receiverId)->first();


                $notiMessage = $senderInfo->first_name . ' ' . $senderInfo->last_name . ' has created new event';
                /// SEnding Notifications
                $notification['receiver_id'] = $receiverId;
                $notification['sender_id'] = $userId;
                $notification['message'] = $notiMessage;
                $notification['action_type'] = 'event';
                $notification['action_id'] = $postObj->id;
                Notification::create($notification);

                // Sending push notification
//                if($recInfo) {
//
//                    $deviceType             = $recInfo->device_type;
//                    $deviceToken            = $recInfo->device_token;
//                    $notification_status    = $recInfo->notification_status;
//
//                    if(!is_null($deviceType) && !is_null($deviceToken) && ($notification_status =='1')) {
//
//                        if($deviceType == 'android') {
//
//                            $postArray = array('title' => 'Broadway Connected', 'message' => $notiMessage, 'sound' => 'default');
//                            // $this->SendPushNotificationAndroid($deviceToken,$postArray);
//                        } else {
//
//                            $apsArray =  array( 'alert' => $notiMessage,
//                                'action_id' => $notification['action_id'],
//                                'action_type'=>$notification['action_type'],
//                                'sound' => 'default');
//
//                            $this->SendPushNotification($deviceToken, $apsArray,$receiverId );
//                        }
//                    }
//                }
                /// PUSH NOTIFICATION WORK ======================================================
            }
        }

        $postObj = Post::with(['publisher', 'tags'])->find($postObj->id);

        return RESTAPIHelper::response($postObj, 'Success', 'Event Published, Check it out on your calendar!');
    }

    public function updateEvent(PostEventUpdateRequest $request)
    {

        $postData = $request->all();
        $emptyObj = new \stdClass();

        $postId = (int) $request->input('post_id');

        $post = Post::find($postId);

        if ($post) {

            $postData['post_type'] = POST::POST_TYPE_EVENT;

            $postData['tags'] = isset($postData['tags']) ? $postData['tags'] : '';

            $currDate = date('Y-m-d H:i:s');
            $postData['published_date'] = $currDate;

            if ($file = $request->hasFile('image')) {
                $file = $request->file('image');

                $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
                $destinationPath = public_path() . '/images/articles/';
                $file->move($destinationPath, $fileName);
                $postData['image'] = $fileName;
            }
            $post->update($postData);
            if (!empty($postData['tags'])) $this->updateTags($postId, $postData['tags']);

            $postObj = Post::with(['publisher', 'tags'])->find($postId);

            return RESTAPIHelper::response($postObj, 'Success', 'Event has been updated successfully');

        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong post_id is required', false);
        }

    }

    public function addToCalendar(AddCalendarRequest $request)
    {

        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $postId = $request->input('post_id');
        $postId = isset($postId) ? $postId : 0;


        $conditions['user_id'] = $userId;
        $conditions['post_id'] = $postId;
        $is_in_calender = UserCalendar::where($conditions)->count();


        $postObj = Post::with(['publisher', 'tags'])->where('id', $postId)->get();
        $responseArray['Posts'] = $postObj;


        if ($is_in_calender > 0) { // removing from favourite list ..
            $favPostObj = UserCalendar::where($conditions)->first();
            //dd($favPostObj);
            $favPostObj->delete();
            return RESTAPIHelper::response($responseArray, 'Success', 'Event removed from calendar successfully');
        } else { // adding to favourite list ..

            UserCalendar::create($postData);
            return RESTAPIHelper::response($responseArray, 'Success', 'Event has been added to calendar successfully');
        }

        //$userCalendarObj = UserCalendar::create($postData);
        //return RESTAPIHelper::response($postObj, 'Success', 'Event has been added to calendar successfully');
    }


    public function addTags($postId, $tags)
    {

        $tags = explode(',', $tags);
        $postTag['post_id'] = $postId;
        //dd($tags);
        if ($tags) {

            foreach ($tags as $tag) {

                $tagInfo = Tag::where('tag', $tag)->first();

                if ($tagInfo) {

                    $postTag['tag_id'] = isset($tagInfo->id) ? $tagInfo->id : '';
                    PostTag::create($postTag);
                } else {

                    $tagData['tag'] = $tag;
                    $tagData['tag_type'] = 'user';
                    $tagInfo = Tag::create($tagData);

                    $postTag['tag_id'] = isset($tagInfo->id) ? $tagInfo->id : '';
                    PostTag::create($postTag);
                }

            }
        }
    }

    public function updateTags($postId, $tags)
    {

        $tags = explode(',', $tags);
        $postTag['post_id'] = $postId;
        //dd($tags);
        if ($tags) {

            PostTag::where('post_id', $postId)->delete();

            foreach ($tags as $tag) {

                $tagInfo = Tag::where('tag', $tag)->first();

                if ($tagInfo) {

                    $postTag['tag_id'] = isset($tagInfo->id) ? $tagInfo->id : '';
                    PostTag::create($postTag);
                } else {

                    $tagData['tag'] = $tag;
                    $tagData['tag_type'] = 'user';
                    $tagInfo = Tag::create($tagData);

                    $postTag['tag_id'] = isset($tagInfo->id) ? $tagInfo->id : '';
                    PostTag::create($postTag);
                }

            }
        }
    }


    public function update(PostUpdateRequest $request)
    {

        $postData = $request->all();
        $userId = $request->input('user_id');
        $postId = $request->input('post_id');
        $userId = isset($userId) ? $userId : 0;

        $deleteImageIds = $request->input('del_img_ids');

        $imgIds = explode(',', $deleteImageIds);

        if ($deleteImageIds) {
            PostImage::destroy($imgIds);
        }


        $is_authorized = $this->checkTokenValidity($userId);
        if ($is_authorized == 0) {
            return RESTAPIHelper::response('', 'Error', 'Invalid Token or User Id', false);
        }

        $postObj = Post::where('id', $postId)->first();

        if ($postObj) {
            $postObj->update($postData);
        }

        if (isset($_FILES['image1']['name'])) {

            if (!empty($_FILES['image1']['name'])) {
                $totalImages = count($_FILES);
                for ($i = 1; $i <= $totalImages; $i++) {

                    $file = $request->file('image' . $i);

                    if (isset($_FILES['image' . $i])) {

                        $ext = substr(strrchr($_FILES['image' . $i]['name'], '.'), 1);
                        $file_name = uniqid() . '.' . $ext;
                        $fileName = \Illuminate\Support\Str::random(12) . '.' . $file->getClientOriginalExtension();
                        $destinationPath = public_path() . '/images/products/';
                        $file->move($destinationPath, $fileName);

                        // Uploading Image ......
                        $image['post_id'] = $postId;
                        $image['image'] = $fileName;
                        PostImage::create($image);
                    }
                }
            }
        }

        return RESTAPIHelper::response($postObj, 'Success', 'Post has been updated successfully');
    }

    public function deletePost(Request $request)
    {

        $emptyObj = new \stdClass();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $postId = $request->input('post_id');
        $postId = isset($postId) ? $postId : 0;

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        if (empty($postId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'post_id is required', false);
        }

        $post = Post::find($postId);
        $post->delete();

        $postType = array('article', 'event');

        $hasNoti = Notification::where('action_id', $postId)->count();
        if ($hasNoti > 0) Notification::where('action_id', $postId)->whereIn('action_type', $postType)->delete();

        return RESTAPIHelper::response($emptyObj, 'Success', 'Post has been deleted successfully');
    }

    public function retrieve(Request $request)
    {
        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $pIds = $groupPostsIds = array();
        $userId = (int) $request->input('user_id');

        $userFollowers = UserFollowing::where('follower_id', $userId)->pluck('followee_id')->toArray();

        $userFollowers[] = $userId;
        $adminUsers = User::where('role_id', '1')->pluck('id')->toArray();
        $userFollowers = array_merge($adminUsers, $userFollowers);

        $postObj = $this->_setMainPartGetPostsQuery();

        $postObj = $postObj->whereIn('user_id', $userFollowers);

        $path = $request->path();

        // Get only articles if url is api/articles otherwise get articles and events
        if ($path == 'api/articles') {
            $postObj = $postObj->where('post_type', '=', Post::POST_TYPE_ARTICLE);
        } else {
            $postObj = $postObj->where(function($query) {
                $query->where('post_type', '=', Post::POST_TYPE_ARTICLE)->orWhere('in_feed', '=', 1);
            });
        }

        ///////// WORKING FOR GROUPS ==============================================
        
        $userGroups = GroupMember::where('user_id', $userId)->pluck('group_id')->toArray();

        if (!empty($userGroups)) {

            $groupPostsIds = POST::where('availability', 'groups')->pluck('id')->toArray();

            foreach ($userGroups as $group) {

                $postIds = POST::whereIn('id', $groupPostsIds)->whereRaw('FIND_IN_SET(' . $group . ',group_ids)=0')->pluck('id')->toArray();

                if (!empty($pIds)) {

                    $pIds = array_unique(array_intersect($pIds, $postIds));

                } else {

                    $pIds = $postIds;
                }
            }
            // dd($pIds);

            $postObj->whereNotIn('id', $pIds);

        } else {

            $postObj = $postObj->whereIn('availability', array('followers', 'all'));
        }
        //////// WORKING FOR GROUPS  ENDS ==============================

        $totalRecords = $postObj->count();

        $responseArray['Posts'] = $postObj->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function trending(Request $request)
    {
        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $postObj = $this->_setMainPartGetPostsQuery();

        $postObj = $postObj->where('in_trending', '=', '1')->orderBy('order_number', 'DESC')->orderBy('created_at', 'DESC');

        $responseArray['TotalRecords'] = $postObj->count();
        $responseArray['Trending'] = $postObj->offset($offset)->limit($limit)->get();

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function articlesByTag(Request $request)
    {
        $offset = (int)$request->input('offset');
        $limit = $request->input('limit');
        $limit = isset($limit) ? (int)$limit : 10;

        $tagId = $request->input('tag_id');

        if (!$tagId) {
            return RESTAPIHelper::response('', 'Error', 'Tag id is required', false);
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS p.*, (
              SELECT GROUP_CONCAT(t.tag SEPARATOR ', ')
              FROM tags AS t, post_tags AS pt
              WHERE pt.post_id = p.id AND pt.tag_id = t.id
            ) AS tags, u.first_name, u.last_name
            FROM posts AS p, post_tags AS pt, users AS u
            WHERE pt.post_id = p.id AND pt.tag_id = $tagId AND p.user_id = u.id AND p.post_type = 'article'
            LIMIT $offset, $limit
        ";

        $articlesData = DB::select($query);
        foreach ($articlesData as $index => $articleData) {
            $articlesData[$index]->publisher = new \stdClass();
            $articlesData[$index]->publisher->first_name = $articleData->first_name;
            $articlesData[$index]->publisher->last_name = $articleData->last_name;
        }

        $responseData['Posts'] = $articlesData;
        $totalRows = DB::select('SELECT FOUND_ROWS() AS total');
        $responseData['TotalRecords'] = $totalRows[0]->total;

        return RESTAPIHelper::response($responseData, 'Success', 'Data retrieved successfully');
    }

    public function userArticles(Request $request)
    {

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $userId = (int) $request->input('user_id');

        $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['post_type'] = POST::POST_TYPE_ARTICLE;
        $conditions['user_id'] = $userId;

        $totalRecords = Post::where($conditions)->count();

        $amountOfCommentsSubquery = $this->_prepareAmountCommentsSubquery();

        $postObj = Post::select('*', $amountOfCommentsSubquery)->with(['publisher', 'tags'])->where($conditions);
        $postObj->orderBy('created_at', 'desc');

        if ($limit > 0) {
            $postObj->offset($offset)->limit($limit);
        }

        $postObj = $postObj->get();

        $responseArray['Posts'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function userEvents(Request $request)
    {

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $post = array();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['post_type'] = POST::POST_TYPE_EVENT;
        $conditions['user_id'] = $userId;

        $totalRecords = Post::where($conditions)->count();

        $postObj = Post::with(['publisher', 'tags'])->where($conditions);
        $postObj->orderBy('created_at', 'desc');

        if ($limit > 0) {
            $postObj->offset($offset)->limit($limit);
        }
        $postObj = $postObj->get();

        $responseArray['Posts'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }


    public function postsDetail(Request $request)
    {

        $post = array();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $postId = $request->input('post_id');
        $postId = isset($postId) ? $postId : 0;

        if ($postId == 0) {
            return RESTAPIHelper::response('', 'Error', 'post id is required', false);
        }

        // $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['id'] = $postId;

        $postObj = Post::with(['publisher', 'tags'])->where('id', $postId)->get();
        //dd($postObj);

        $responseArray['Posts'] = $postObj;
        //  $responseArray[] = $postObj;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }


    public function markFavouriteUnfavourite(Request $request)
    {

        $emptyObj = new \StdClass();
        $postData = $request->all();

        $userId = (int) $request->input('user_id');

        $postId = (int) $request->input('post_id');

        if (empty($postId) || is_null($postId)) {
            return RESTAPIHelper::response('', 'Error', 'Invalid Token or Post Id', false);
        }

        $amountCommentsSubquery = $this->_prepareAmountCommentsSubquery();

        $postObj = Post::select('*', $amountCommentsSubquery)->with(['publisher', 'tags'])->where('id', $postId)->first();

        if ($postObj) {

            $conditions['user_id'] = $userId;
            $conditions['post_id'] = $postId;
            $is_favourite = FavouritePost::where($conditions)->count();

            $responseArray['Posts'][] = $postObj;

            if ($is_favourite > 0) { // removing from favourite list ..
                $favPostObj = FavouritePost::where($conditions)->first();
                //dd($favPostObj);
                $favPostObj->delete();
                return RESTAPIHelper::response($responseArray, 'Success', 'Removed from favourite list');
            } else { // adding to favourite list ..

                FavouritePost::create($postData);

                $receiverId = $postObj->user_id;
                $post_type = $postObj->post_type;
                $postTitle = $postObj->title;

                if ($receiverId != $userId) {

                    /// PUSH NOTIFICATION WORK ======================================================
                    $recInfo = User::where('id', $receiverId)->first();
                    $senderInfo = User::where('id', $userId)->first();

                    $notiMessage = $senderInfo->first_name . ' ' . $senderInfo->last_name . ' liked ' . $postTitle;
                    /// SEnding Notifications
                    $notification['receiver_id'] = $receiverId;
                    $notification['sender_id'] = $userId;
                    $notification['message'] = $notiMessage;
                    $notification['action_type'] = 'like';
                    $notification['action_id'] = $userId;

                    $oldNotification = Notification::where('receiver_id', $receiverId)
                        ->where('sender_id', $userId)
                        ->where('action_type', 'like')
                        ->where('action_id', $userId)->first();

                    if ($oldNotification) {
                        $oldNotification->delete();
                    }

                    Notification::create($notification);


                    if ($recInfo) {

                        $deviceType = $recInfo->device_type;
                        $deviceToken = $recInfo->device_token;
                        $notification_status = $recInfo->notification_status;

                        if (!is_null($deviceType) && !is_null($deviceToken) && ($notification_status == '1')) {

                            if ($deviceType == 'android') {

                                $postArray = array('title' => 'Broadway Connected', 'message' => $notiMessage, 'sound' => 'default');
                                // $this->SendPushNotificationAndroid($deviceToken,$postArray);
                            } else {

                                $apsArray = array('alert' => $notiMessage,
                                    'action_id' => $notification['action_id'],
                                    'action_type' => $notification['action_type'],
                                    'sound' => 'default');

                                $this->SendPushNotification($deviceToken, $apsArray, $receiverId);
                            }
                        }
                    }
                    /// PUSH NOTIFICATION WORK ======================================================
                }


                return RESTAPIHelper::response($responseArray, 'Success', 'Added to user favourite successfully');
            }


        } else {


            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong post id given', false);
        }

    }

    public function userFavouritePosts(Request $request)
    {
        $post = array();
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $postIds = array('0');

        $postIdsSoFar = FavouritePost::where('user_id', $userId)->lists('post_id');
        if ($postIdsSoFar) {
            $postIds = $postIdsSoFar->toArray();
        }

        if ($userId == 0) {
            return RESTAPIHelper::response('', 'Error', 'user id is required', false);
        }


        $totalRecords = Post::whereIn('id', $postIds)->count();

        $amountPostsSubquery = $this->_prepareAmountCommentsSubquery();

        $postObj = Post::select('*', $amountPostsSubquery)
            ->with(['publisher', 'tags'])
            ->whereIn('id', $postIds)
            ->offset($offset)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();


        $responseArray['Posts'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfullys');
    }


    // Admin Apis
    public function changeStatus(Request $request)
    {
        $post = array();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        if ($postData['id'] == 0) {
            return RESTAPIHelper::response('', 'Error', 'post id is required', false);
        }
        if ($postData['status'] == '') {
            return RESTAPIHelper::response('', 'Error', 'status is required', false);
        }

        $updateData['status'] = $postData['status'];

        if ($postData['status'] == POST::POST_STATUS_SCHEDULED) {
            if (empty($postData['published_date']) || !check_date($postData['published_date'])) {
                return RESTAPIHelper::response('', 'Error', 'published_date isn\'t valid format', false);
            }

            if (isset($_COOKIE['timezone_offset'])) {
                $offset = (int) $_COOKIE['timezone_offset'];
                $updateData['published_date'] = datetime_from_local_to_utc($postData['published_date'], $offset);
            } else {
                $updateData['published_date'] = datetime_from_est_to_utc($postData['published_date']);
            }
        }

        // $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['id'] = $postData['id'];

        $postObj = Post::where($conditions)->first();

        if ($postObj) {
            $post = $postObj->update($updateData);

        } else {

            return RESTAPIHelper::response('', 'Error', 'wrong post id is given', false);
        }

        return RESTAPIHelper::response($post, 'Success', 'Product has been updated successfully');
    }

    public function repostPost(Request $request)
    {

        $post = array();
        $postData = $request->all();
        $id = $request->input('id');
        $id = isset($id) ? $id : 0;

        $currDate = date('Y-m-d H:i:s');
        if ($id == 0) {
            return RESTAPIHelper::response('', 'Error', 'post id is required', false);
        }

        $conditions['id'] = $id;

        $postObj = Post::where($conditions)->first();

        if ($postObj) {

            $updateData['created_at'] = $currDate;
            $updateData['reposted_at'] = $currDate;
            $post = $postObj->update($updateData);

        } else {

            return RESTAPIHelper::response('', 'Error', 'wrong post id is given', false);
        }

        $responseArray = $post;

        return RESTAPIHelper::response('', 'Success', 'Post has been updated successfully');
    }

    // Admin Apis Ends ..

    public function upload(Request $request)
    {

        if (isset($_FILES['image']['name'])) {
            $image = array();

            $file = $request->file('image');

            $ext = substr(strrchr($_FILES['image']['name'], '.'), 1);
            $file_name = uniqid() . '.' . $ext;
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/products/';
            $file->move($destinationPath, $fileName);
            // Uploading Image ......

            $image['image'] = 'http://35.160.175.165/portfolio/garagediscount/public/images/products/' . $fileName;
            $mess = 'saved';

        } else {


            $image = new \stdClass();
            $mess = 'no image';
        }

        return RESTAPIHelper::response($image, 'Success', $mess);
    }

    public function getCode(CodeRequest $request)
    {


        $postData = $request->all();

        $code = str_random(5);

        $responseArray['Posts'] = $code;


        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function insertComment(PostCommentCreateRequest $request)
    {

        $postData = $request->all();
        $emptyObj = new \stdClass();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $post = Post::find($postData['post_id']);

        if ($post) {

            $postObj = PostComment::create($postData);

            $data['data'] = PostComment::with(['user', 'post'])->where('id', $postObj->id)->get();

            return RESTAPIHelper::response($data, 'Success', 'Post comment has been saved successfully');

        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong post id given', false);
        }
    }

    public function retrieveComments(Request $request)
    {


        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $post = array();
        $emptyObj = new \stdClass();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $postId = $request->input('post_id');
        $postId = isset($postId) ? $postId : 0;

        if (empty($postId) || is_null($postId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'post_id is required', false);
        }


        $conditions['post_id'] = $postId;

        $totalRecords = PostComment::where($conditions)->count();

        $postObj = PostComment::with(['user', 'post'])->where($conditions)
            ->orderBy('created_at', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();


        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function search(Request $request)
    {
        $tag_id = $request->input('tag_id');
        $tag_id = isset($tag_id) ? $tag_id : '';

        $result = array();
        $emptyObj = new \stdClass();

        $searchType = $request->input('search_type');
        $searchType = isset($searchType) ? $searchType : 0;

        if (empty($searchType) || is_null($searchType)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'searchType is required', false);
        }

        if ($searchType == 'user') {

            $result = $this->SearchUser($request);

        } else if ($searchType == 'tag') {

            $result = $this->SearchTags($request);

        } else if ($searchType == 'article') {

            $result = $this->SearchArticles($request);

        } else if ($searchType == 'event') {

            $result = $this->SearchEvents($request);

        } else if ($searchType == 'all') {


            if ($tag_id > 0) {

                $result = $this->SearchPostsByTag($request);
            } else {
                $result = $this->SearchPosts($request);
            }


        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong search type given', false);
        }


        return RESTAPIHelper::response($result, 'Success', 'Data retrieved successfully');
    }

    public function searchUser($request)
    {


        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;


        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $result = array();
        $emptyObj = new \stdClass();
        $postData = $request->all();

        $keyword = $request->input('keyword');
        $keyword = isset($keyword) ? $keyword : 0;

        $postId = $request->input('post_id');
        $postId = isset($postId) ? $postId : 0;

        //$uIds[]     = $userId;


        $conditions['status'] = User::STATUS_ACTIVE;
        $conditions['private_profile'] = 0;
        $conditions['role_id'] = '2';
        $userObj = User::where($conditions);
        $userObj = $userObj->where('id', '!=', $userId);

        if (isset($keyword)) $userObj = $userObj->where('first_name', 'LIKE', '%' . $keyword . '%');
        // ->orWhere('last_name', 'LIKE', '%'.$keyword.'%');


        $totalRecords = $userObj->count();

        $postObj = $userObj
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }

    public function searchTags($request)
    {

        $result = array();
        $emptyObj = new \stdClass();
        $postData = $request->all();

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;


        $keyword = $request->input('keyword');
        $keyword = isset($keyword) ? $keyword : 0;


        $totalRecords = Tag::where('tag', 'LIKE', '%' . $keyword . '%')->count();

        $tagObj = Tag::where('tag', 'LIKE', '%' . $keyword . '%')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $tagObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }

    public function searchArticles($request)
    {
        $offset = (int) $request->input('offset');
        $limit = (int) $request->input('limit');
        $limit = $limit ? $limit : 10;

        $keyword = $request->input('keyword');
        $keyword = isset($keyword) ? $keyword : 0;

        $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['post_type'] = POST::POST_TYPE_ARTICLE;
        $postObj = Post::where($conditions);

        if ($keyword) {
            $postObj = $postObj->where(function($query) use($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }
        
        $totalRecords = $postObj->count();

        $amountCommentsSubquery = $this->_prepareAmountCommentsSubquery();
        
        $postObj = $postObj->select('*', $amountCommentsSubquery)
            ->with(['publisher', 'tags'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }

    public function searchEvents($request)
    {

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $result = array();
        $emptyObj = new \stdClass();
        $postData = $request->all();

        $keyword = $request->input('keyword');
        $keyword = isset($keyword) ? $keyword : 0;

        $conditions['status'] = POST::POST_STATUS_APPROVED;
        $conditions['post_type'] = POST::POST_TYPE_EVENT;
        $postObj = Post::where($conditions);

        if (isset($keyword)) $postObj = $postObj->where('title', 'LIKE', '%' . $keyword . '%');
        //->orWhere('description', 'LIKE', '%'.$keyword.'%');

        $totalRecords = $postObj->count();

        $postObj = $postObj->with(['publisher', 'tags'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }

    public function searchPosts($request)
    {
        $offset = (int) $request->input('offset');
        $limit = (int) $request->input('limit');
        $limit = $limit ? $limit : 50;
        $keyword = $request->input('keyword');
        
        $conditions['status'] = POST::POST_STATUS_APPROVED;
        $postObj = Post::where($conditions);

        if ($keyword) {
            $postObj = $postObj->where(function($query) use($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }

        $totalRecords = $postObj->count();

        $amountCommentsSubquery = $this->_prepareAmountCommentsSubquery();
        
        $postObj = $postObj->select('*', $amountCommentsSubquery)
            ->with(['publisher', 'tags'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }

    public function SearchPostsByTag($request)
    {
        $offset = (int) $request->input('offset');
        $limit = (int) $request->input('limit');
        $limit = $limit ? $limit : 50;
        $tag_id = (int) $request->input('tag_id');
        $keyword = $request->input('keyword');

        $conditions['status'] = POST::POST_STATUS_APPROVED;

        $amountCommentsSubquery = $this->_prepareAmountCommentsSubquery();

        $postObj = Post::select('*', $amountCommentsSubquery)->where($conditions);

        if ($keyword) {
            $postObj = $postObj->where(function($query) use($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }
        
        if ($tag_id > 0) {
            $postIds = PostTag::where('tag_id', $tag_id)->pluck('post_id')->toArray();
        }
        
        $totalRecords = $postObj->whereIn('id', $postIds)->count();
        
        $postObj = $postObj->whereIn('id', $postIds)
            ->with(['publisher', 'tags'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['data'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return $responseArray;
    }


    public function getCalenderEvent(Request $request)
    {

        $emptyObj = new \stdClass();
        $post = $adminUsers = $pIds = array();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $is_admin = $request->input('is_admin');
        $is_admin = isset($is_admin) ? $is_admin : 0;

        $start_date = $request->input('start_date');
        $start_date = isset($start_date) ? $start_date : '';


        if (empty($start_date) || is_null($start_date)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'start_date is required', false);
        }

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        $userFollowers = UserFollowing::where('follower_id', $userId)->pluck('followee_id')->toArray();

        $adminUsers = User::where('role_id', '1')->pluck('id')->toArray();
        $userFollowers = array_merge($adminUsers, $userFollowers);

        $userCalendarPost = UserCalendar::where('user_id', $userId)->pluck('post_id')->toArray();
        //$adminUserPost    = Post::where('posting_type','admin')->pluck('id')->toArray();


        $userFollowers[] = (int)$userId;


        $conditions['post_type'] = POST::POST_TYPE_EVENT;
        $conditions['status'] = POST::POST_STATUS_APPROVED;

        if ($is_admin > 0) {
            $conditions['posting_type'] = 'admin';

            //$adminUsers = User::where('role_id','1')->pluck('id')->toArray();
            // $userFollowers =  array_merge($adminUsers,$userFollowers);
        }


        $startLimit = date("Y-m-1", strtotime($start_date . '+6 months'));
        $endLimit = date("Y-m-30", strtotime($start_date . '-6 months'));


//        $totalRecords = Post::where($conditions)
//            ->whereBetween('start_date', array($endLimit,$startLimit) )
//            ->where(function ($query) use ($userFollowers,$userCalendarPost) {
//                    $query->whereIn('user_id', $userFollowers )
//                        ->orWhereIn('id', $userCalendarPost );
//                })
//            ->count();

        // $orderBy['start_date'] = 'asc';
        $orderBy['created_at'] = 'desc';

//        $postObj = Post::with(['publisher','tags'])->where($conditions)
//            ->whereBetween('start_date', array($endLimit,$startLimit) )
//            ->where(function ($query) use ($userFollowers,$userCalendarPost) {
//                $query->whereIn('user_id', $userFollowers )
//                    ->orWhereIn('id', $userCalendarPost );
//            })
//            ->orderBy('start_date','asc')
//            ->orderBy('created_at','desc')
//            ->offset($offset)
//            ->limit($limit)
//            ->get();


        $postObj = Post::with(['publisher', 'tags'])->where($conditions)
            ->whereBetween('start_date', array($endLimit, $startLimit))
            ->where(function ($query) use ($userFollowers, $userCalendarPost) {
                $query->whereIn('user_id', $userFollowers)
                    ->orWhereIn('id', $userCalendarPost);
            });

        ///////// WORKING FOR GROUPS ==============================================

        $userGroups = GroupMember::where('user_id', $userId)->pluck('group_id')->toArray();


        if (!empty($userGroups)) {

            $groupPostsIds = POST::where('availability', 'groups')->pluck('id')->toArray();


            foreach ($userGroups as $group) {

                $postIds = POST::whereIn('id', $groupPostsIds)->whereRaw('FIND_IN_SET(' . $group . ',group_ids)=0')->pluck('id')->toArray();

                if (!empty($pIds)) {
                    $pIds = array_unique(array_intersect($pIds, $postIds));
                } else {

                    $pIds = $postIds;
                }

            }
            // dd($pIds);


            $postObj->whereNotIn('id', $pIds);

        } else {

            $postObj = $postObj->whereIn('availability', array('followers', 'all'));
        }

        //////// WORKING FOR GROUPS  ENDS ==============================

        $totalRecords = $postObj->count();

        $postObj = $postObj->orderBy('start_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['Posts'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function getSingleDateCalender(Request $request)
    {

        $emptyObj = new \stdClass();
        $post = $adminUsers = $pIds = array();
        $postData = $request->all();
        $userId = $request->input('user_id');
        $userId = isset($userId) ? $userId : 0;

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit = $request->input('limit');
        $limit = isset($limit) ? $limit : 10;

        $start_date = $request->input('start_date');
        $start_date = isset($start_date) ? $start_date : '';

        $is_admin = $request->input('is_admin');
        $is_admin = isset($is_admin) ? $is_admin : 0;

        if (empty($start_date) || is_null($start_date)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'start_date is required', false);
        }

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }


        $userFollowers = UserFollowing::where('follower_id', $userId)->pluck('followee_id')->toArray();
        $adminUsers = User::where('role_id', '1')->pluck('id')->toArray();
        $userFollowers = array_merge($adminUsers, $userFollowers);

        $userCalendarPost = UserCalendar::where('user_id', $userId)->pluck('post_id')->toArray();

//        $adminUserPost    = Post::where('posting_type','admin')->pluck('id')->toArray();
//
//        if(!empty($userCalendarPost)) {
//
//            $userCalendarPost = array_intersect($userCalendarPost,$adminUserPost);
//        } else {
//
//            $conditions['posting_type']    = 'user';
//        }


        if ($is_admin > 0) {
            $conditions['posting_type'] = 'admin';
            // $adminUsers     = User::where('role_id','1')->pluck('id')->toArray();
            // $userFollowers  =  array_merge($adminUsers,$userFollowers);
        }

        $userFollowers[] = (int)$userId;

        $conditions['post_type'] = POST::POST_TYPE_EVENT;
        $conditions['status'] = POST::POST_STATUS_APPROVED;

        //$startLimit          = date("Y-m-d 00:00:00", strtotime($start_date));
        //$endLimit            = date("Y-m-d 23:59:59", strtotime($start_date));


        $postObj = Post::with(['publisher', 'tags'])
            ->where($conditions)
            ->where('start_date', '=', $start_date)
            ->where(function ($query) use ($userFollowers, $userCalendarPost) {
                $query->whereIn('user_id', $userFollowers)
                    ->orWhereIn('id', $userCalendarPost);
            });


        ///////// WORKING FOR GROUPS ==============================================

        $userGroups = GroupMember::where('user_id', $userId)->pluck('group_id')->toArray();


        if (!empty($userGroups)) {

            $groupPostsIds = POST::where('availability', 'groups')->pluck('id')->toArray();


            foreach ($userGroups as $group) {

                $postIds = POST::whereIn('id', $groupPostsIds)->whereRaw('FIND_IN_SET(' . $group . ',group_ids)=0')->pluck('id')->toArray();

                if (!empty($pIds)) {
                    $pIds = array_unique(array_intersect($pIds, $postIds));
                } else {

                    $pIds = $postIds;
                }

            }
            // dd($pIds);


            $postObj->whereNotIn('id', $pIds);

        } else {

            $postObj = $postObj->whereIn('availability', array('followers', 'all'));
        }

        //////// WORKING FOR GROUPS  ENDS ==============================

        $totalRecords = $postObj->count();

        // $orderBy['start_date'] = 'asc';
        $orderBy['created_at'] = 'desc';

        $postObj = $postObj
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $responseArray['Posts'] = $postObj;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function getPostsForPush()
    {
        $posts = Post::select('id', 'title')
            ->where('post_type', Post::POST_TYPE_ARTICLE)
            ->orderBy('created_at', 'desc')
            ->get();

        return RESTAPIHelper::response($posts, 'Success', 'Data retrieved successfully');
    }

    public function getEventPostsForPush()
    {
        $posts = Post::select('id', 'title')
            ->where('post_type', Post::POST_TYPE_EVENT)
            ->orderBy('created_at', 'desc')
            ->get();

        return RESTAPIHelper::response($posts, 'Success', 'Data retrieved successfully');
    }

    public function frontArticle(Request $request, $id)
    {
        $postData = $request->all();
        $posts = Post::with(['publisher', 'tags'])->where('post_type', Post::POST_TYPE_ARTICLE)->find($id);

        //dd($posts->share_url);
        if ($posts) {

            return frontend_view('article', compact('posts'));

        } else {

            abort(403, 'Unauthorized action.');
        }
    }

    public function frontEvent(Request $request, $id)
    {
        
        $postData = $request->all();
        $posts = Post::with(['publisher', 'tags'])->where('post_type', Post::POST_TYPE_ARTICLE)->find($id);

        if ($posts) {

            return frontend_view('event', compact('posts'));

        } else {

            abort(403, 'Unauthorized action.');
        }
    }

    private function _setMainPartGetPostsQuery()
    {
        $inStatus = [POST::POST_STATUS_APPROVED, POST::POST_STATUS_SCHEDULED];
        $selectCreatedAtQuery = DB::raw('IF(status = "' . POST::POST_STATUS_SCHEDULED . '", published_date, created_at) as created_at ');
        $numberOfCommentsQuery = $this->_prepareAmountCommentsSubquery();

//        $current_time = get_current_est_date('Y-m-d H:i:s');
        $current_time = date('Y-m-d H:i:s');
        $whereRaw = '(status = "' . POST::POST_STATUS_APPROVED . '" OR (status = "' . POST::POST_STATUS_SCHEDULED . '" AND published_date <= "' . $current_time . '"))';

        return Post::select('*', $selectCreatedAtQuery, $numberOfCommentsQuery)
            ->with(['publisher', 'tags'])
            ->whereIn('status', $inStatus)
            ->whereRaw($whereRaw);
    }

    private function _prepareAmountCommentsSubquery() {
        return DB::raw('(SELECT count(*) FROM `post_comments` WHERE `post_comments`.`post_id` = `posts`.`id`) AS number_of_comments');
    }
}
