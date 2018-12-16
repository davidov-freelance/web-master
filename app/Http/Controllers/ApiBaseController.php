<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
// use App\Helpers\RESTAPIHelper;
// use JWTAuth;
use App\Http\Traits\JWTUserTrait;
use App\Helpers\RESTAPIHelper;
use App\User;
use Illuminate\Support\Facades\Redirect;
use App\Notification;

class ApiBaseController extends Controller {

	/**
	 * Extract token value from request
	 *
	 * @return string
	 */
	protected function extractToken($request=false) {
		return JWTUserTrait::extractToken($request);
	}

	/**
	 * Return User instance or false if not exist in DB
	 *
	 * @return mixed
	 */
	protected function getUserInstance($request=false) {
		return JWTUserTrait::getUserInstance($request);
	}

	protected function checkTokenValidity($userId) {

		if($userId > 0 ) {
			$userData = $this->getUserInstance();

			if ($userData) {

				if($userData->id != $userId) {

					return 0;
				}

			}
		}
		return 1;
	}

	function SendPushNotificationAndroid($device, $postArray) {

		//$url = 'https://android.googleapis.com/gcm/send';
		$url            = 'https://fcm.googleapis.com/fcm/send';
		$serverApiKey   = "AAAAaAVsqS0:APA91bFpczX6doKwgiat7R4x_MAyzKfqBXRkigfuUExKui8-YGRgMqQRz0kFgwdw8G6lKvSo5UJEhwbigH4JkoqBcZNYTspJAdN0ODNjLVO-rQJ6sLJpQjk7t2Mm7EVLFuqS1SI0tINq";

		$serverApiKey   = " AIzaSyCWyx2rdho9baxPJzZwb_fIuWEbKIXT7U0";
		$reg            = $device;


		$postArray['url']       = $url;

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

	function SendPushNotification($device, $apsArray,$receiverId=0) {


		$badge              = $this->getBadgeCount($receiverId);
		$apsArray['badge']  = $badge;

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

		//$apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
		$apns = stream_socket_client('ssl://gateway.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);


		//dd($apns);
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;



		$result =  fwrite($apns, $apnsMessage);


//		if (!$result)
//			echo 'Message not delivered' . PHP_EOL;
//		else
//			echo 'Message successfully delivered' . PHP_EOL;
//
//		dd($result);
		fclose($apns);


	}


	function getBadgeCount($receiverId){

		$unreadCount = Notification::where('receiver_id', $receiverId)->where('is_read', 0)->count();

		return $unreadCount;
	}

	public function _preparePostResponse($pObj, $request) {

		$data['id']             = $pObj['id'];
		$data['title']          = $pObj['title'];
		$data['description']    = $pObj['description'];
		$data['post_type']      = $pObj['post_type'];
		$data['time_ago']       = $pObj['time_ago'];
		$data['post_image']     = $pObj['post_image'];
		$data['is_like']        = $pObj['is_like'];
		$data['likes']          = $pObj['likes'];
		$data['created_at']     = $pObj->created_at;
		$data['updated_at']     = $pObj['updated_at'];
		$data['publisher']      = $pObj->publisher;
		$data['tags']           = $pObj->tags;

		return $data;
	}

	public function __construct(Request $request) {

		$requestParam   = Request::all();
		$uId 			= isset($requestParam['user_id']) ? $requestParam['user_id'] : '';

		if($uId > 0) {

			$user = User::find($uId);
			//dd($user);
			if(!isset($user)){

				//$this->FailureResponse(); // not working directly
				Redirect::to('api/failure')->send();
			} else if($user){

				if($user->status == '0') {
					Redirect::to('api/failure/block')->send();
				}

			}
		}
	}

	public function FailureResponse() {

		return RESTAPIHelper::response('', 'Error', 'Your account is deactivated by administrator', false,2);
	}

	public function FailureResponseBlock() {

		return RESTAPIHelper::response('', 'Error', 'Your account is blocked by administrator, Contact Administrator to activate your account info@broadwayconnected.com ', false,1);
	}

}