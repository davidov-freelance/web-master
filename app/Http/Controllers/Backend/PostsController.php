<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // to get admin info/ authenticated user
use App\Http\Requests\Backend\PostCreateRequest;
use App\Http\Requests\Backend\PostEventCreateRequest;
use App\Helpers\RESTAPIHelper;

use Config;
use App\Group;
use App\GroupMember;
use App\User;
use App\Post;
use App\Tag;
use App\Category;
use App\PostTag;
use App\Notification;
use App\Show;
use App\ArticleShow;
use App\PostComment;


class PostsController extends BackendController
{
    ////////// ARTICLE SECTION =================================
    public function getIndex()
    {
        $currentDatetime = $this->getUserLocalTime();

        $condition['post_type'] = Post::POST_TYPE_ARTICLE;

        $publishedDateOffsetSubquery = $this->_getPublishedDateOffsetSubquery();

        $posts = Post::select('*', $publishedDateOffsetSubquery)
            ->with(['publisher', 'tags'])
            ->where($condition)
            ->orderBy('created_at', 'DESC')
            ->get();

        return backend_view('posts.articles', compact('posts', 'currentDatetime'));
    }

    public function add()
    {
        $showOptions = $this->_getShowOptions();
        $userOptions = $this->_getUsersOptions();

        $condition['tag_type'] = TAG::TAG_TYPE_ADMIN;
        $tags = Tag::where($condition)->pluck('tag', 'id');
        $groups = Group::pluck('name', 'id')->toArray();

        $titleMaxLength = Config::get('constants.back.articleTitleMaxLength');
        return backend_view(
            'posts.add_article',
            compact('tags', 'groups', 'titleMaxLength', 'showOptions', 'userOptions')
        );
    }

    public function create(PostCreateRequest $request)
    {
        $postData = $request->all();
        
        $postData['post_type'] = Post::POST_TYPE_ARTICLE;

        if (empty($postData['status'])) {

            $postData['status'] = 'pending';
        }
        
        if ($postData['status'] == Post::POST_STATUS_APPROVED) {
            
            $currDate = date('Y-m-d H:i:s');
            $postData['published_date'] = $currDate;
            
        } else if ($postData['status'] == Post::POST_STATUS_SCHEDULED) {
            $postData['published_date'] = $this->_collectConvertPublishedDate($postData);
        }

        $sendNotification = 0;

        if ($postData['posting_type'] == 'admin' && isset($postData['send_notification']) && $postData['status'] == Post::POST_STATUS_APPROVED) {

            $sendNotification = 1;
        }

        $groups = isset($postData['groups']) ? $postData['groups'] : array();

        if (!empty($groups)) {

            $postData['group_ids'] = implode(',', $groups);
        }

        if ($request->hasFile('image')) {
            $postData['image'] = $this->saveImage($request->file('image'), Config::get('constants.front.dir.articlesImagePath'));
        }

        $id = Post::create($postData)->id;

        if (!empty($postData['tags'])) {

            $this->_addTags($id, $postData['tags']);
        }
        
        if (!empty($postData['show_ids'])) {
            
            $this->_addShows($id, $postData['show_ids']);
        }

        if ($sendNotification == 1) {

            $this->_sendPush($id, $postData['availability'], $postData);
        }

        session()->flash('alert-success', 'Article has been added successfully!');
        return redirect('backend/articles/add/');
    }

    public function edit(Post $post)
    {
        $postTag = PostTag::where('post_id', $post->id)->pluck('tag_id');
        $condition['tag_type'] = TAG::TAG_TYPE_ADMIN;
        $tags = Tag::where($condition)->pluck('tag', 'id');
        $groups = Group::pluck('name', 'id')->toArray();

        $post = $this->_explodeConvertPublishedDate($post);

        $titleMaxLength = Config::get('constants.back.articleTitleMaxLength');
        $showOptions = $this->_getShowOptions();
        $userOptions = $this->_getUsersOptions();
        
        return backend_view(
            'posts.edit',
            compact('post', 'tags', 'postTag', 'groups', 'titleMaxLength', 'showOptions', 'userOptions')
        );
    }

    public function update(PostCreateRequest $request, Post $post)
    {
        $postData = $request->all();

        if ($request->hasFile('image')) {
            $postData['image'] = $this->saveImage($request->file('image'), Config::get('constants.front.dir.articlesImagePath'));
        }

        if (empty($postData['status'])) {
            $postData['status'] = 'pending';
        }

        if ($postData['status'] == Post::POST_STATUS_SCHEDULED) {
            $postData['published_date'] = $this->_collectConvertPublishedDate($postData);
        } elseif ($postData['status'] == Post::POST_STATUS_APPROVED) {
            if ($post->status == Post::POST_STATUS_PENDING) {
                $postData['published_date'] = date('Y-m-d H:i:s');
            } else {
                $postData['published_date'] = $post->created_at;
            }
        } else {
            $postData['published_date'] = null;
        }

        $post->update($postData);

        if (isset($postData['tags'])) {
            $this->_updateTags($post->id, $postData['tags']);
        }

        if (isset($postData['show_ids'])) {
            $this->_updateShows($post->id, $postData['show_ids']);
        }

        session()->flash('alert-success', 'Article has been updated successfully!');
        return redirect('backend/articles');
    }

    public function getUserArticles(Request $request, $id)
    {

        $userInfo = User::find($id);
        if ($userInfo) {

            $condition['post_type'] = Post::POST_TYPE_ARTICLE;
            $condition['user_id'] = $id;
            $posts = Post::with(['publisher', 'tags'])->where($condition)->get();
            return backend_view('posts.articles', compact('posts'));
        }
    }

    public function getGroupArticles(Request $request, $id)
    {

        $membersId = GroupMember::where('group_id', $id)->pluck('user_id')->toArray();

        $condition['post_type'] = Post::POST_TYPE_ARTICLE;
        $posts = Post::with(['publisher', 'tags'])
            ->where($condition)
            ->whereIn('user_id', $membersId)->get();
        return backend_view('posts.articles', compact('posts'));

    }


    ////////// EVENT SECTION =================================

    public function getEventIndex()
    {
        $currentDatetime = $this->getUserLocalTime();

        $condition['post_type'] = Post::POST_TYPE_EVENT;

        $publishedDateOffsetSubquery = $this->_getPublishedDateOffsetSubquery();

        $posts = Post::select('*', $publishedDateOffsetSubquery)
            ->with(['publisher', 'tags'])
            ->where($condition)
            ->orderBy('created_at', 'DESC')
            ->get();

        return backend_view('posts.events', compact('posts', 'currentDatetime'));
    }

    public function getTrending()
    {
        $currentDatetime = $this->getUserLocalTime();

        $condition['in_trending'] = 1;

        $publishedDateOffsetSubquery = $this->_getPublishedDateOffsetSubquery();

        $posts = Post::select('*', $publishedDateOffsetSubquery)
            ->with(['publisher', 'tags'])
            ->where($condition)
            ->orderBy('order_number', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        return backend_view('posts.trending', compact('posts', 'currentDatetime'));
    }

    // Admin Apis
    public function changeInTrending(Request $request)
    {
        $this->validate($request, array(
            'id' => 'required|integer|min:1',
            'in_trending' => 'required|integer|min:0|max:1',
        ));

        $postData = $request->all();

        $conditions['id'] = $postData['id'];

        $updateData['in_trending'] = (int)$postData['in_trending'];

        if ($post = Post::where($conditions)->update($updateData)) {
            return RESTAPIHelper::response($post, 'Success', 'Post has been updated successfully');
        }

        return RESTAPIHelper::response('', 'Error', 'wrong post id is given', false);
    }
    
    public function changeInFeed(Request $request)
    {
        $this->validate($request, array(
            'id' => 'required|integer|min:1',
            'in_feed' => 'required|integer|min:0|max:1',
        ));

        $postData = $request->all();

        $conditions['id'] = $postData['id'];

        $updateData['in_feed'] = (int) $postData['in_feed'];

        if ($post = Post::where($conditions)->update($updateData)) {
            return RESTAPIHelper::response($post, 'Success', 'Post has been updated successfully');
        }

        return RESTAPIHelper::response('', 'Error', 'wrong post id is given', false);
    }

    public function changeTrendingOrder(Request $request)
    {
        $this->validate($request, array(
            'id' => 'required|integer|min:1',
            'order_number' => 'required|integer|min:0',
        ));

        $postData = $request->all();

        $conditions['id'] = $postData['id'];

        $updateData['order_number'] = (int)$postData['order_number'];

        if ($post = Post::where($conditions)->update($updateData)) {
            return RESTAPIHelper::response($post, 'Success', 'Post has been updated successfully');
        }

        return RESTAPIHelper::response('', 'Error', 'wrong post id is given', false);
    }

    public function getUserEventIndex(User $id)
    {
        $condition['post_type'] = Post::POST_TYPE_EVENT;
        $condition['user_id'] = $id->id;
        $posts = Post::with(['publisher', 'tags'])->where($condition)->get();
        return backend_view('posts.events', compact('posts'));
    }

    public function getGroupEvents(Request $request, $id)
    {
        $membersId = GroupMember::where('group_id', $id)->pluck('user_id')->toArray();

        $condition['post_type'] = Post::POST_TYPE_EVENT;
        $posts = Post::with(['publisher', 'tags'])
            ->where($condition)
            ->whereIn('user_id', $membersId)->get();

        return backend_view('posts.events', compact('posts'));
    }

    public function addEvent()
    {
        $condition['tag_type'] = TAG::TAG_TYPE_ADMIN;
        $tags = Tag::where($condition)->pluck('tag', 'id');
        $categories = Category::pluck('category_name', 'id');
        $groups = Group::pluck('name', 'id')->toArray();

        $titleMaxLength = Config::get('constants.back.articleTitleMaxLength');
        return backend_view('posts.add_event', compact('tags', 'categories', 'groups', 'titleMaxLength'));
    }

    public function editEvent(Post $post)
    {
        /* if ( !$user->isAdmin() )
          abort(404); */
        $postTag = PostTag::where('post_id', $post->id)->pluck('tag_id')->toArray();
        $condition['tag_type'] = TAG::TAG_TYPE_ADMIN;
        $tags = Tag::where($condition)->pluck('tag', 'id');
        $categories = Category::pluck('category_name', 'id');
        $groups = Group::pluck('name', 'id')->toArray();

        $post = $this->_explodeConvertPublishedDate($post);

        $titleMaxLength = Config::get('constants.back.articleTitleMaxLength');
        return backend_view('posts.edit-event', compact('post', 'tags', 'categories', 'postTag', 'groups', 'titleMaxLength'));
    }

    public function createEvent(PostEventCreateRequest $request)
    {
        $userId = 0;
        $adminUser = Auth::User();
        
        if ($adminUser) {
            
            $userId = $adminUser->id;
        }

        $postData = $request->all();
        $postData['post_type'] = POST::POST_TYPE_EVENT;

        $tags = isset($postData['tags']) ? $postData['tags'] : '';
    
        $sendNotification = 0;
    
        if ($postData['posting_type'] == 'admin' && isset($postData['send_notification']) && $postData['status'] == Post::POST_STATUS_APPROVED) {
            
            $sendNotification = 1;
        }

        $postData['user_id'] = $userId;

        if (empty($postData['status'])) {

            $postData['status'] = 'pending';
        }

        $postData['published_date'] = $this->_collectConvertPublishedDate($postData);

        $groups = isset($postData['groups']) ? $postData['groups'] : array();

        if (!empty($groups)) {

            $postData['group_ids'] = implode(',', $groups);
        }

        if ($request->hasFile('image')) {
            $postData['image'] = $this->saveImage($request->file('image'), Config::get('constants.front.dir.articlesImagePath'));
        }

        $id = Post::create($postData)->id;

        if (!empty($tags)) {
            
            $this->_addTags($id, $tags);
        }

        if ($sendNotification) {

            $this->_sendPush($id, $postData['availability'], $postData);
        }

        session()->flash('alert-success', 'Event has been added successfully!');
        return redirect('backend/events');
    }

    public function updateEvent(PostEventCreateRequest $request, Post $post)
    {

        $postData = $request->all();

        if ($request->hasFile('image')) {
            $postData['image'] = $this->saveImage($request->file('image'), Config::get('constants.front.dir.articlesImagePath'));
        }

        if (empty($postData['status'])) {

            $postData['status'] = 'pending';
        }

        $postData['published_date'] =
        $postData['published_date'] = $this->_collectConvertPublishedDate($postData);

        $post->update($postData);

        if (isset($postData['tags'])) $this->_updateTags($post->id, $postData['tags']);

        session()->flash('alert-success', 'Event has been updated successfully!');
        return redirect('backend/events');
    }

    private function _explodeConvertPublishedDate($post)
    {
        // Need for old data
        if ($post['published_date'] == '0000-00-00 00:00:00') {
            $post['published_date'] = $post['created_at'];
        }

        $offset = $this->getUserOffset();
        $post['published_date'] = datetime_from_utc_to_local($post['published_date'], $offset);

        $post['published_time'] = substr($post['published_date'], 11, 5);
        $post['published_date'] = substr($post['published_date'], 0, 10);
        return $post;
    }

    private function _addTags($postId, $tags)
    {
        if (empty($tags)) {
            return;
        }
        
        $postTag['post_id'] = $postId;

        foreach ($tags as $tag) {

            $postTag['tag_id'] = isset($tag) ? $tag : '';
            PostTag::create($postTag);
        }
    }

    private function _updateTags($postId, $tags)
    {
        PostTag::where('post_id', $postId)->delete();
        
        if ($tags) {
            $this->_addTags($postId, $tags);
        }
    }
    
    private function _updateShows($postId, $shows)
    {
        $postShows['post_id'] = $postId;

        if ($shows) {

            ArticleShow::where('post_id', $postId)->delete();
    
            $this->_addShows($postId, $shows);
        }
    }
    
    private function _addShows($postId, $showsIds) {
        $insertShowData = [];
    
        $createdAt = get_created_at();
        foreach ($showsIds as $showId) {
            $insertShowData[] = ['post_id' => $postId, 'show_id' => $showId, 'created_at' => $createdAt];
        }
    
        ArticleShow::insert($insertShowData);
    }

    public function destroy(Post $post)
    {

        $postId = $post->id;
        $post->delete();

        $postType = array('article', 'event');
        $hasNoti = Notification::where('action_id', $postId)->count();
        if ($hasNoti > 0) Notification::where('action_id', $postId)->whereIn('action_type', $postType)->delete();

        session()->flash('alert-success', 'Post has been deleted successfully!');
        return redirect('backend/articles');
    }
    
    public function removeComment(PostComment $comment)
    {
        $comment->delete();
        return redirect('backend/articles/detail/' . $comment->post_id);
    }

    public function details(Post $post)
    {

        $post = Post::with('comments.user')->where('id', $post->id)->first();
        //dd($post);
        return backend_view('posts.detail', compact('post'));
    }

    public function EventDetail(Post $post)
    {

        $post = Post::with('comments.user')->where('id', $post->id)->first();
        return backend_view('posts.event-detail', compact('post'));
    }
    
    private function _getShowOptions()
    {
        $currentDate = date('Y-m-d');
        $shows = Show::where('closing_at', '>', $currentDate)
            ->orWhere('closing_at', '=', '0000-00-00 00:00:00')
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        return $shows;
    }
    
    private function _sendPush($actionId, $availability, $postData)
    {
        $adminUser = Auth::User();
        $senderId = $adminUser->id;
        
        if ($availability == 'all') {
            
            $users = User::where('device_token', '!=', '')
                ->whereNotNull('device_token')
                ->get();
            
        } else if ($availability == 'groups') {
            
            $groupIds = isset($postData['groups']) ? $postData['groups'] : array();
            
            if ($groupIds) {
                
                $uIds = GroupMember::whereIn('group_id', $groupIds)->pluck('user_id')->toArray();
                
                $users = User::whereIn('id', $uIds)
                    ->where('device_token', '!=', '')
                    ->whereNotNull('device_token')
                    ->get();
            }
        }
        
        if (!empty($users)) {
            foreach ($users as $receiver) {
                
                $notification['receiver_id'] = $receiver->id;
                $notification['sender_id'] = $senderId;
                $notification['message'] = $postData['notification_description'];
                $notification['title'] = $postData['notification_title'];
                $notification['action_type'] = $postData['post_type'];
                $notification['action_id'] = $actionId;
                
                $this->_sendPushToUser($notification, $receiver);
            }
        }
    }
    
    private function _sendPushToUser($notificationData, $receiverData)
    {
        /// PUSH NOTIFICATION WORK ======================================================
        Notification::create($notificationData);
        
        if ($receiverData) {
            
            $deviceType = $receiverData->device_type;
            $deviceToken = $receiverData->device_token;
            $notification_status = $receiverData->notification_status;
            
            if (!is_null($deviceType) && !is_null($deviceToken) && ($notification_status == '1')) {
                $apsArray = [
                    'alert' => [
                        'title' => $notificationData['title'],
                        'body' => $notificationData['message']
                    ],
                    'action_id' => $notificationData['action_id'],
                    'action_type' => $notificationData['action_type'],
                    'sound' => 'default'
                ];
                
                $this->SendPushNotification($deviceToken, $apsArray, $receiverData->id);
            }
        }
        /// PUSH NOTIFICATION WORK ======================================================
    }

    private function _getPublishedDateOffsetSubquery()
    {
        $offset = $this->getUserOffset();
        return DB::raw("DATE_SUB(`published_date`, INTERVAL $offset hour) AS published_date");
    }

    private function _collectConvertPublishedDate($postData) {
        $offset = $this->getUserOffset();
        return datetime_from_local_to_utc($postData['published_date'] . ' ' . $postData['published_time'], $offset);
    }

    private function _getUsersOptions() {
        $users = User::select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        if (!count($users)) {
            return false;
        }

        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user->id] = "{$user->first_name} {$user->last_name}";
        }

        return $userOptions;
    }
}
