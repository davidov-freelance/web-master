<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\PushNotificationRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\Auth;
//use Validator;

use App\Notification;
use App\User;
use App\GroupMember;
use App\TagsNotificationSubscribers;

class PushNotificationController extends BackendController
{
    public function add()
    {
        return backend_view('push.add');
    }
    
    public function sendNotification(PushNotificationRequest $request)
    {
        // Disable notifications for my test server
        if ($_SERVER['HTTP_HOST'] == 'brodway.dm') {
            session()->flash('alert-success', 'Notification has been sent successfully!');
            return redirect('backend/push/send/');
        }
        
        $adminUser = Auth::User();
        $senderId = 0;
        $action_id = 0;
        $action_type = 'admin';
        
        if ($adminUser) {
            
            $adminData = $adminUser->toArray();
            $senderId = $adminData['id'];
        }
        
        $postData = $request->all();
        
        //dd($postData);
        
        $title = isset($postData['title']) ? $postData['title'] : 'title';
        $message = isset($postData['message']) ? $postData['message'] : 'message';
        $type = isset($postData['type']) ? $postData['type'] : 'message';
        $notification_type = isset($postData['notification_type']) ? $postData['notification_type'] : 'message';
        $post_id = isset($postData['post_id']) ? $postData['post_id'] : '0';
        
        if (empty($postData['uids'])) {
            
            session()->flash('alert-danger', 'Select at least one user/group to send push notification');
            return redirect('backend/push/send/');
        }
        
        
        if ($type == 'group') {
            
            $groupUsers = GroupMember::whereIn('group_id', $postData['uids'])->pluck('user_id')->toArray();
            
        } else if ($type == 'tags') {
            
            $groupUsers = TagsNotificationSubscribers::whereIn('tag_id', $postData['uids'])
                ->groupBy('user_id')
                ->pluck('user_id')
                ->toArray();
            
        } else {
            $groupUsers = $postData['uids'];
        }
        
        $users = User::find($groupUsers)->makeVisible('device_token')->toArray();
        
        /// sending notification related to articles/events
        if ($notification_type == 'article' || $notification_type == 'event') {
            
            if (empty($post_id)) {
                
                session()->flash('alert-danger', 'Select Post Id');
                return redirect('backend/push/send/');
            }
            
            $action_type = $notification_type;
            $action_id = (int)$post_id;
        }
        
        foreach ($users as $user) {
            
            $notificationStatus = $user['notification_status'];
            $deviceType = $user['device_type'];
            $deviceToken = $user['device_token'];
            
            if ($notificationStatus == 1) {

                /// SEnding Notifications
                $notification['receiver_id'] = $user['id'];
                $notification['sender_id'] = $senderId;
                $notification['message'] = $message;
                $notification['action_type'] = $action_type;
                $notification['action_id'] = $action_id;
                $notificationId = Notification::create($notification)->id;
                
                if (($deviceType == 'android') && !empty($deviceToken)) {

                    //$postArray = array('title' => $title, 'message' => $message);
                    //$this->SendPushNotificationAndroid($deviceToken, $postArray);
                    
                } else if (($deviceType == 'ios') && !empty($deviceToken)) {
                    
                    $apsArray = array(
                        'alert' => $message,
                        'message' => '',
                        'sound' => 'default',
                        'action_type' => $action_type,
                        'action_id' => (int)$action_id
                    );
                    
                    if ($notification_type == 'generic') {
                        
                        $apsArray['notification_id'] = $notificationId;
                    }
                    
                    $this->SendPushNotification($deviceToken, $apsArray, $user['id']);
                    // dd($apsArray);
                }
            }
        }
        
        session()->flash('alert-success', 'Notification has been sent successfully!');
        return redirect('backend/push/send/');
    }
    
}
