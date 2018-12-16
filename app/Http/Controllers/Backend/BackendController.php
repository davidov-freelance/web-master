<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\Notification;
use App\Admin;


class BackendController extends Controller
{
    public $editor_permission_not_allowed = array(
        'backend/admin',
        'backend/admin/add',
        'backend/admin/edit',
        'backend/admin/create',
        'backend/admin/change-to-user',
        'backend/product',
        'backend/dashboard',
        'backend/setting/profile',
        'backend/setting/setting',
        'backend/user',
        'backend/user/edit/',
        'backend/user/add',
        'backend/user/create',
        'backend/user/profile',
        'backend/user/changeStatus',
        'backend/user/change-to-admin',
        'backend/comment/remove',
        'backend/events',
        'backend/events/edit',
        'backend/events/add',
        'backend/events/group',
        'backend/events/create',
        'backend/events/detail',
        'backend/events/remove',
        'backend/trending',
        'backend/questions',
        'backend/questions/users',
        'backend/questions/add',
        'backend/questions/create',
        'backend/questions/edit',
        'backend/shows',
        'backend/shows/news',
        'backend/shows/add',
        'backend/shows/create',
        'backend/shows/edit',
        'backend/shows/gross',
        'backend/shows/gross/add',
        'backend/shows/gross/edit',
        'backend/shows/gross/save',
        'backend/shows/gross/check',
        'backend/shows/theaters',
        'backend/shows/theaters/add',
        'backend/shows/theaters/create',
        'backend/shows/theaters/edit',
        'backend/shows/gross/ajax-save',
        'backend/faq',
        'backend/faq/edit',
        'backend/faq/add',
        'backend/faq/update',
        'backend/cms',
        'backend/cms/edit',
        'backend/cms/add',
        'backend/cms/create',
        'backend/badges',
        'backend/badges/edit',
        'backend/badges/add',
        'backend/badges/create',
        'backend/categories',
        'backend/categories/edit',
        'backend/categories/add',
        'backend/categories/create',
        'backend/tags',
        'backend/tags/edit',
        'backend/tags/add',
        'backend/tags/create',
        'backend/fields',
        'backend/fields/edit',
        'backend/fields/add',
        'backend/fields/create',
        'backend/push/send',
        'backend/push/sendpush',
        'backend/contacts',
        'backend/groups',
        'backend/group/edit',
        'backend/group/update',
        'backend/group/new',
        'backend/group/create',
        'backend/report',
        'backend/posts/changePostInFeed',
        'backend/questions/remove',
        'backend/questions/create',
        'backend/questions/get-answered-users',
    );

    public $editor_permission_exceptions = array(
        'backend/articles/update',
        'backend/articles/remove',
        'backend/articles',
        'backend/comment/remove',
    );

    public $moderator_permission_not_allowed = array(
        'backend/dashboard',
        'backend/categories/add',
        'backend/articles/add',
        'backend/events/add',
        'backend/tags/add',
        'backend/group/new',
        'backend/push/send',
        'backend/faq/edit/faq',
        'backend/admin',
        'backend/admin/add',
        'backend/user/change-to-admin'
    );

    public $subadmin_permission_not_allowed = array(
        'backend/dashboard',
        'backend/admin',
        'backend/admin/add',
        'backend/user/change-to-admin',
        'backend/admin/change-to-user',
    );

    public function __construct(Route $route)
    {

        $is_allow = $this->_isAllowed($route);
        $currPage = Request::capture()->path();

        if ($is_allow == 0 && $currPage != 'backend/dashboard') {
            abort(403, 'Unauthorized action.');

        } else if ($is_allow == 0) {

            $user = Auth::user();

            if ($user->admin_role == Admin::ADMIN_ROLE_EDITOR) {
                header('Location: articles');
                exit;
            }

            header('Location: user');
            exit();
        }

    }

    function SendPushNotificationAndroid($device, $postArray)
    {

        //$url = 'https://android.googleapis.com/gcm/send';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverApiKey = "AAAAaAVsqS0:APA91bFpczX6doKwgiat7R4x_MAyzKfqBXRkigfuUExKui8-YGRgMqQRz0kFgwdw8G6lKvSo5UJEhwbigH4JkoqBcZNYTspJAdN0ODNjLVO-rQJ6sLJpQjk7t2Mm7EVLFuqS1SI0tINq";

        $serverApiKey = " AIzaSyCWyx2rdho9baxPJzZwb_fIuWEbKIXT7U0";
        $reg = $device;


        $postArray['url'] = $url;

        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $serverApiKey
        );

        $data = array(
            'registration_ids' => array($reg),
            'data' => $postArray
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        if ($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    function SendPushNotification($device, $apsArray, $receiverId = 0)
    {

        // Disable notifications for my test server
//        if ($_SERVER['HTTP_HOST'] == 'brodway.dm') {
//            return;
//        }

        $badge = $this->getBadgeCount($receiverId);
        $apsArray['badge'] = $badge;

        $ckpem = public_path() . '/certificates/dis.pem';
        //$ckpem = public_path() . '/certificates/dev.pem';

        $payload['aps'] = $apsArray; // array('alert' => $title, 'message' => $msg, 'sound' => 'default');

        $payload = json_encode($payload);

        $options = array('ssl' => array(
            'local_cert' => $ckpem,
            'passphrase' => '1'
        ));

        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, $options);
        $apns = stream_socket_client('ssl://gateway.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
        //$apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);

        //dd($apns);

        $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;

        $result = fwrite($apns, $apnsMessage);


//		if (!$result)
//			echo 'Message not delivered' . PHP_EOL;
//		else
//			echo 'Message successfully delivered' . PHP_EOL;

        fclose($apns);
    }

    function getBadgeCount($receiverId)
    {

        $unreadCount = Notification::where('receiver_id', $receiverId)->where('is_read', 0)->count();

        return $unreadCount;
    }

    function isMyOwnProfile($id)
    {

        $adminUser = Auth::User();
        $allow = 0;
        if ($adminUser) {

            $adminData = $adminUser->toArray();
            $adminid = $adminData['id'];

            if ($adminid == $id) $allow = 1;
        }

        return $allow;
    }

    function saveImage($file, $path)
    {
        $fileName = \Illuminate\Support\Str::random(12) . '.' . $file->getClientOriginalExtension();
        $destinationPath = base_path() . '/' . $path;
        $file->move($destinationPath, $fileName);
        $showData['image'] = $fileName;
        return $fileName;
    }

    function getUserOffset()
    {
        return isset($_COOKIE['timezone_offset']) ? $_COOKIE['timezone_offset'] : 0;
    }

    function getUserLocalTime()
    {
        $offset = $this->getUserOffset();
        return datetime_from_utc_to_local('now', $offset);
    }

    private function _isAllowed($route)
    {
        $currPage = $this->_getRoute();
        $method = \Request::method();

        $adminUser = Auth::User();

        if ($adminUser) {

            $adminData = $adminUser->toArray();
            $is_subadmin = $adminData['is_subadmin'];
            $admin_role = $adminData['admin_role'];

            if ($is_subadmin == 1) {

                if ($admin_role == Admin::ADMIN_ROLE_EDITOR) {

                    if (in_array($method, array('DELETE', 'PUT'))) {

                        return in_array($currPage, $this->editor_permission_exceptions);
                    }

                    return !in_array($currPage, $this->editor_permission_not_allowed);

                } else if ($admin_role == Admin::ADMIN_ROLE_MODERATOR) {

                    if (in_array($currPage, $this->moderator_permission_not_allowed) || in_array($method, array('DELETE', 'PUT'))) {

                        return false;
                    }

                } else if ($admin_role == Admin::ADMIN_ROLE_SUB) {
                    return !in_array($currPage, $this->subadmin_permission_not_allowed);
                }
            }

            return true;
        }

        return false;
    }

    private function _getRoute()
    {
        $path = Request::capture()->path();
        return preg_replace('/\/\d+$/', '', $path);
    }
}
