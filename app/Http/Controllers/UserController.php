<?php
namespace App\Http\Controllers;

use App\UserFollowing;
use App\UserSetting;
use Illuminate\Http\Request;
use Hash;
use Config;

use JWTAuth;
use Tymon\JWTAuth\Contracts;

use App\User;
use App\Report;
use App\Post;
use App\Notification;
use App\Group;
use App\UserBadge;

use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Http\Requests\Frontend\UserVerifyRequest;
use App\Http\Requests\Frontend\ResendCodeRequest;
use App\Http\Requests\Frontend\SocialLoginRequest;
use App\Http\Requests\Frontend\ChangePasswordRequest;
use App\Helpers\RESTAPIHelper;
use App\Helpers\TwilioHelper;
use App\Helpers\EmailHelper;

use Validator;

class UserController extends ApiBaseController {


    public function guestUserToken(Request $request)
    {
        $projectname            =   $request->input('project_name');
        $token                  =  base64_encode($projectname);

        $result['token']        = $token;
        return RESTAPIHelper::response( $result);
    }

    public function register(UserRegisterRequest $request)
    {
        $input = $request->all();

        if ($request->hasFile('profile_picture')) {
            $imageName =  \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = public_path(Config::get('constants.front.dir.profilePicUploadPath'));
            $request->file('profile_picture')->move($path, $imageName);
            $input['profile_picture'] = $imageName;
        }
		
        $input['password'] = Hash::make($input['password']);
        $input['role_id']  = User::ROLE_MEMBER;
        
        if (!empty($input['phone'])) {
            $input['verification_code'] = generate_digits(Config::get('constants.front.verificationCodeLength'));
        }

        User::create($input);
        
        if (!empty($input['phone'])) {
            $this->_sendVerificationCode($input['phone'], $input['verification_code']);
        }
        
        return $this->login($request,'1');
    }
    
    private function _sendVerificationCode($phone, $code) {
        $smsMessage = 'Verification code: ' . $code;
        return TwilioHelper::sendSMS($phone, $smsMessage);
    }
    
    public function verifyAccount(UserVerifyRequest $request) {
        $verificationData = $request->all();
    
        $conditions = array(
            'id' => $verificationData['user_id'],
            'verification_code' => $verificationData['code'],
        );
    
        if(User::where($conditions)->update(array('is_verified' => 1))) {
            return RESTAPIHelper::response( true,'Success','User has been successfully verified', false);
        }
        
        return RESTAPIHelper::response( false,'Error','User verification failed', false);
    }
    
    public function resendCode(ResendCodeRequest $request) {
        $resendData = $request->all();
        
        $userData = User::find($resendData['user_id']);
        
        if ($userData->is_verified) {
            return RESTAPIHelper::response( false,'Error','User already is verified', false);
        }
    
        $userData->verification_code = generate_digits(Config::get('constants.front.verificationCodeLength'));
        
        if (!empty($resendData['phone'])) {
            $userData->phone = $resendData['phone'];
        } else if (empty($userData->phone)) {
            return RESTAPIHelper::response( false,'Error','User doesn\'t have the phone number for verification', false);
        }
    
        $userData->update();
        
        $this->_sendVerificationCode($userData->phone, $userData->verification_code);
        return RESTAPIHelper::response( true,'Success','Verification code has been resent successfully', false);
    }

    public function login(Request $request,$is_register = '')
    {
        $requestInfo      = $request->all();
        $input            = $request->only(['email', 'password']);
        $input['role_id'] = User::ROLE_MEMBER;

        $device_token = isset($requestInfo['device_token']) ? $requestInfo['device_token'] : 0;
        $device_type  = isset($requestInfo['device_type']) ? $requestInfo['device_type'] : 0;

        if (!$token = JWTAuth::attempt($input)) {
            return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
        }

        $userData = JWTAuth::toUser($token)->toArray();

        if(!empty($device_token) && !empty($device_type)) {
            $device['user_id']          = $userData['id'];
            $device['device_token']     = $device_token;
            $device['device_type']      = $device_type;
            $this->addUpdateDeviceToken($device);
        }

        $result   = $this->_prepareUserResponse($userData, $token);

        if($result){

            if($result['status'] == '0'){

                return RESTAPIHelper::response('', 'Error', 'Your account is blocked by administrator, Contact Administrator to activate your account info@broadwayconnected.com ', false,1);
            }
        }

        if($is_register) {
            $dev_msg = "User has been successfully registered";
        } else {
            $dev_msg = "User has been successfully logged in";
        }

        return RESTAPIHelper::response( $result ,'Success',$dev_msg);
    }

    public function socialLogin(SocialLoginRequest $request) {

        $input      = $request->all();
        $emptyObj   = new \stdClass();

        $socialMediaId = isset($input['social_media_id']) ? $input['social_media_id'] : 0;

        $device_token = isset($input['device_token']) ? $input['device_token'] : 0;
        $device_type  = isset($input['device_type']) ? $input['device_type'] : 0;

        if(empty($socialMediaId) ) {

            return RESTAPIHelper::response($emptyObj, 'Error', 'Wrong social id provided');
        }

        $userData = User::where('social_media_id', $input['social_media_id'])->first();

        if (!empty($userData)) {

            if (!$token = JWTAuth::fromUser($userData)) {
                return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
            }

            $token              = $token;
            if(!empty($device_token) && !empty($device_type)) {
                $device['user_id']          = $userData->id;
                $device['device_token']     = $device_token;
                $device['device_type']      = $device_type;
                $this->addUpdateDeviceToken($device);
            }

            $result             = $this->_prepareUserResponse($userData, $token);

            return RESTAPIHelper::response($result, 'Success', 'Logged In Successfully');

        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'Not registered');
        }
    }

    public function forgotPassword(Request $request) {

        $emptyObj = new \stdClass();

        if(!$request->input('email') )
        {
            return RESTAPIHelper::response('','Error','Parameter Missing', false);
        }

        $userRequested = User::where([
            'email'   =>  $request->input('email')
        ])->first();

        if ( !$userRequested ) {
            return RESTAPIHelper::response('Email not found in database.', false, 'invalid_email');
        }

        $passwordGenerated = \Illuminate\Support\Str::random(12);

        $userRequested->password = Hash::make( $passwordGenerated );
        $userRequested->save();
    
    

        // Send reset password email
        $emailBody = "You have requested to reset a password of your account, please find your new generated password below:

            New Password: " . $passwordGenerated . "

            Thanks.";
        
        $emailSubject = 'Reset Password - Broadway Connected';
        
        EmailHelper::sendMail($userRequested->email, $emailSubject, $emailBody);

        $message = 'We have sent you new password in your email, please check your inbox as well as spam/junk folder.';
        return RESTAPIHelper::response(  $emptyObj ,'Success',$message );
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate( $this->extractToken() );

        return RESTAPIHelper::emptyResponse();
    }

    public function updateUser(Request $request) {

        $input                  = $request->all();
        $userId                 = $input['user_id'];
        $headline_position      = isset($input['headline_position'])    ?  $input['headline_position'] : '';
        $previous_position      = isset($input['previous_position'])    ?  $input['previous_position'] : '';
        $field_of_work          = isset($input['field_of_work'])        ?  $input['field_of_work'] : '';
        $field_of_work_id       = isset($input['field_of_work_id'])     ?  $input['field_of_work_id'] : '';
        $user                   = User::find($userId);

        if($user) {

            $dataToUpdate = array_filter([
                'first_name'         => $request->get('first_name', null),
                'last_name'          => $request->get('last_name', null),
                'full_name'          => $request->get('full_name', null),
                'country'            => $request->get('country', null),
                'city'               => $request->get('city', null),
                'phone'              => $request->get('phone', null),
                'dob'                => $request->get('dob', null),
                'handle'             => $request->get('handle', null),
                'field_of_work'      => $request->get('field_of_work', null),
                'previous_position'  => $request->get('previous_position', null)
            ]);

            $dataToUpdate['headline_position'] = $headline_position;
            $dataToUpdate['previous_position'] = $previous_position;
              $dataToUpdate['field_of_work']      = $field_of_work;
            $dataToUpdate['field_of_work_id'] = $field_of_work_id;


            if ($request->hasFile('profile_picture')) {
                $imageName  = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
                $path       = public_path(Config::get('constants.front.dir.profilePicUploadPath'));
                $request->file('profile_picture')->move($path, $imageName);

                $dataToUpdate['profile_picture'] = $imageName;
            }


            if (empty($dataToUpdate))
                return RESTAPIHelper::response('Nothing to update', false);

            $user->update($dataToUpdate);
            
            if (!$token = JWTAuth::fromUser($user)) {
                return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
            }

            $userData   = JWTAuth::toUser($token)->toArray();
            $result     = $this->_prepareUserResponse($userData, $token);

            $dev_msg    = "Profile updated successfully";
            return RESTAPIHelper::response( $result ,'Success',$dev_msg);


        } else {
            // if no user object found .....
            return RESTAPIHelper::response('no user found for the given id', false);
        }

        //return RESTAPIHelper::emptyResponse();
    }

    public function changePassword(ChangePasswordRequest $request) {

        $input             = $request->all();
        $userId            = isset($input['user_id']) ? $input['user_id'] : 0;
        $oldPassword       = isset($input['old_password']) ? $input['old_password'] : 0;

        if(empty($userId)) {return RESTAPIHelper::response('user_id is required', false); }

//        $is_authorized      = $this->checkTokenValidity($userId);
//        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }

        $user               = User::find($userId);

        if($user) {

             $notification_status = isset($input['notification_status']) ? $input['notification_status'] : null;
             $private_profile     = isset($input['private_profile']) ? $input['private_profile'] : null;

            if(!is_null($notification_status)) {
                $dataToUpdate['notification_status'] = $notification_status;
            }
            if(!is_null($private_profile)) {
                $dataToUpdate['private_profile'] = $private_profile;
            }
           // dd($dataToUpdate);

            if ($request->has('password') && $request->get('password', '') !== '') {

                // checking old password is correct ....
                if ($request->has('old_password') && $request->get('old_password', '') !== '') {

                    $loginattemp['email']   =  $user->email;
                    $loginattemp['password'] =  $oldPassword;

                    // checking old Password ....
                    if (!$token = JWTAuth::attempt($loginattemp)) {
                        return RESTAPIHelper::response('Wrong old password provided', false);
                    }

                    $dataToUpdate['password'] = \Hash::make($request->get('password'));

                }
            }

            if (empty($dataToUpdate)) return RESTAPIHelper::response('Nothing to update', false);


            $user->update($dataToUpdate);



            $userData = User::find($user->id);
            if (!$token = JWTAuth::fromUser($userData)) {
                return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
            }

            $result = $this->_prepareUserResponse($userData, $token);


        } else {

            return RESTAPIHelper::response('Wrong user id provided', false);
        }

        return RESTAPIHelper::response( $result,'Success','Record updated successfully', false);

    }

    public function addUpdateDeviceToken($data) {

        $user_id           = $data['user_id'];
        $device_token      = $data['device_token'];
        $device_type       = $data['device_type'];

        // Removing / Updating device token to empty , account that have same device token as supplied in input ...
        $users = User::where('device_token',$device_token)->where('device_type',$device_type)->get();

        if($users) {
            $updateArray['device_token'] = '';
            User::where('device_token',$device_token)->where('device_type',$device_type)->update($updateArray);

        }

        $user = User::find($user_id);
        $user->update($data);

    }

    public function userSetting(Request $request) {

        $emptyObj          = new \stdClass();
        $input             = $request->all();
        $userId            = isset($input['user_id']) ? $input['user_id'] : 0;


        if($userId == 0) {return RESTAPIHelper::response($emptyObj,'Error','Invalid User Id', false); }

        $user = User::find($userId);


        $input['chef_id'] = $userId;

        if($user) {
            $userSetting = UserSetting::where('chef_id',$userId)->first();

                if($userSetting) {

                    $userSetting->update($input);
                } else {

                    UserSetting::create($input);
                }

            //dd($input);

            if (!$token = JWTAuth::fromUser($user)) {
                return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
            }

            $userData = JWTAuth::toUser($token)->toArray();
            $result = $this->_prepareUserResponse($userData, $token);


            $dev_msg = "Profile updated successfully";
            return RESTAPIHelper::response( $result ,'Success',$dev_msg);


        } else {
            // if no user object found .....
            return RESTAPIHelper::response('no user found for the given id', false);
        }

        //return RESTAPIHelper::emptyResponse();
    }

    public function _prepareUserResponse($userData , $token = '') {



        $data['id']                         = $userData['id'];
        $data['user_id']                    = $userData['id'];
        $data['first_name']                 = $userData['first_name'];
        $data['last_name']                  = $userData['last_name'];
        $data['email']                      = $userData['email'];
        $data['phone']                      = $userData['phone'];
        $data['country']                    = $userData['country'];
        $data['city']                       = $userData['city'];
        $data['dob']                        = $userData['dob'];
       // $data['longitude']                  = $userData['longitude'];
        $data['status']                     = $userData['status'];

        $data['profile_image']              = $userData['profile_image'];
        $data['handle']                     = $userData['handle'];
        $data['field_of_work']              = $userData['field_of_work']; //
        $data['field_of_work_id']           = $userData['field_of_work_id'];
        $data['headline_position']          = $userData['headline_position'];
        $data['previous_position']          = $userData['previous_position'];
        $data['social_media_id']            = $userData['social_media_id'];
        $data['social_media_platform']      = $userData['social_media_platform'];
        $data['is_featured']                = $userData['is_featured'];
        $data['private_profile']            = $userData['private_profile'];
        $data['notification_status']        = $userData['notification_status'];
        $data['is_verified']                = $userData['is_verified'];

       /// appended counts fields ...
        $data['follower_count']             = $userData['follower_count'];
        $data['following_count']            = $userData['following_count'];
        $data['event_count']                = $userData['event_count'];
        $data['article_count']              = $userData['article_count'];
        $data['is_following']               = $userData['is_following'];


        if(!empty($token)) {
            $data['token'] = $token;
        }
        return $data;
    }


    public function followUnfollow(Request $request) {

        $emptyObj       = new \StdClass();
        $postData       = $request->all();
        $followerId     = $request->input('follower_id');
        $followerId     = isset($followerId) ? $followerId : 0;

        $followeeId     = $request->input('followee_id');
        $followeeId     = isset($followeeId) ? $followeeId : 0;


        if (empty($followerId) || is_null($followerId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'Follower id is required', false);
        }

        if (empty($followeeId) || is_null($followeeId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'Followee Id is required', false);
        }


        //////////////// Is Already Following check ......................
        $conditions['follower_id']  = $followerId;
        $conditions['followee_id']  = $followeeId;

        $isFollowed           = UserFollowing::where($conditions)->count();


        if ($isFollowed > 0) { // alread following , un following user  ..

            $followObj = UserFollowing::where($conditions)->first();
            $followObj->delete();

            return RESTAPIHelper::response($emptyObj, 'Success', 'Unfollow successfully');

        } else { // adding to favourite list ..

            UserFollowing::create($postData);



            /// PUSH NOTIFICATION WORK ======================================================
            $recInfo                    = User::where('id',$followeeId)->first();
            $senderInfo                 = User::where('id',$followerId)->first();

            $notiMessage = $senderInfo->first_name.' '.$senderInfo->last_name.' started following you';
            /// SEnding Notifications
            $notification['receiver_id'] = $followeeId;
            $notification['sender_id']   = $followerId;
            $notification['message']     = $notiMessage;
            $notification['action_type'] = 'follow';
            $notification['action_id']   = $followerId;
            Notification::create($notification);

            if($recInfo) {

                $deviceType             = $recInfo->device_type;
                $deviceToken            = $recInfo->device_token;
                $notification_status    = $recInfo->notification_status;

                if(!is_null($deviceType) && !is_null($deviceToken) && ($notification_status =='1')) {

                    if($deviceType == 'android') {

                        $postArray = array('title' => 'Broadway Connected', 'message' => $notiMessage, 'sound' => 'default');
                        // $this->SendPushNotificationAndroid($deviceToken,$postArray);
                    } else {

                        $apsArray =  array( 'alert' => $notiMessage,
                            'action_id' => $notification['action_id'],
                            'action_type'=>$notification['action_type'],
                            'sound' => 'default');

                        $this->SendPushNotification($deviceToken, $apsArray , $followeeId );
                    }
                }
            }
            /// PUSH NOTIFICATION WORK ======================================================


            return RESTAPIHelper::response($emptyObj, 'Success', 'Start following successfully');
        }
    }

    public function getFollowings(Request $request)
    {
        $users                      = array();
        $emptyObj                   = new \stdClass();
        $offset                     = $request->input('offset');
        $offset                     = isset($offset) ? $offset : 0;
        $limit                      = $request->input('limit');
        $limit                      = isset($limit) ? $limit : 10;
        $userId                     = isset($request['user_id']) ? $request['user_id'] : NULL ;
        $conditions                 = array();

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        $keyword                    = $request->input('keyword');
        $keyword                    = isset($keyword) ? $keyword : 0;

        $conditions['follower_id']  = $userId;
        $followeeIds                = UserFollowing::where($conditions)->pluck('followee_id')->toArray();

        $userObj                    = User::whereIn('id',$followeeIds);

        if(!empty($keyword)) {
            $userObj->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%'.$keyword.'%' )
                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%');
            });
        }

        $totalRecords               = $userObj->count();
        $userObj = $userObj
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        foreach ($userObj as $uObj) {
            $users[] = $this->_prepareUserResponse($uObj);
        }

        $responseArray['records']         = $users;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');

    }

    public function getFollowers(Request $request)
    {

        $users                      = array();
        $emptyObj                   = new \stdClass();
        $offset                     = $request->input('offset');
        $offset                     = isset($offset) ? $offset : 0;
        $limit                      = $request->input('limit');
        $limit                      = isset($limit) ? $limit : 10;
        $userId                     = isset($request['user_id']) ? $request['user_id'] : NULL ;
        $conditions                 = array();

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        $conditions['followee_id']  = $userId;
        $followerIds                = UserFollowing::where($conditions)->pluck('follower_id')->toArray();

        $keyword                    = $request->input('keyword');
        $keyword                    = isset($keyword) ? $keyword : 0;

        $userObj                    = User::whereIn('id',$followerIds);

        if(!empty($keyword)) {
            $userObj->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%'.$keyword.'%' )
                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%');
            });
        }

        $totalRecords = $userObj->count();

        $userObj = $userObj
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();


        foreach ($userObj as $uObj) {
            $users[] = $this->_prepareUserResponse($uObj);
        }

        $responseArray['records']         = $users;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');

    }

    public function getSuggestedUser(Request $request)
    {

        $users                      = $uIds = array();
        $emptyObj                   = new \stdClass();
        $offset                     = $request->input('offset');
        $offset                     = isset($offset) ? $offset : 0;
        $limit                      = $request->input('limit');
        $limit                      = isset($limit) ? $limit : 10;
        $userId                     = isset($request['user_id']) ? $request['user_id'] : NULL ;
        $conditions                 = array();

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        $categoriesIds = Post::where('user_id',$userId)
            ->where('post_type','event')->pluck('category_id')->toArray();


        if(!empty($categoriesIds)){

            $uIds = Post::whereIn('category_id',$categoriesIds)
                ->where('post_type','event')->pluck('user_id')->toArray();
        }

        $keyword     = $request->input('keyword');
        $keyword     = isset($keyword) ? $keyword : 0;

        $conditions['status']  = '1';
        $conditions['role_id']  = '2';
        $conditions['private_profile']   = 0;

        $userObj = User::where($conditions);
        $userObj =  $userObj->where('id','!=',$userId);

        if(!empty($keyword)) {

            $userObj = $userObj->where('first_name', 'like', "%".$keyword."%");
        }


        if(!empty($uIds)){

            $uIds = array_unique($uIds);

            $userObj = $userObj->whereIn('id',$uIds);
        }

        $totalRecords = $userObj->count();

        $userObj = $userObj
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();


        foreach ($userObj as $uObj) {
            $users[] = $this->_prepareUserResponse($uObj);
        }

        $responseArray['records']         = $users;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');

    }

    public function getDiscoverUser(Request $request)
    {

        $users                      = array();
        $emptyObj                   = new \stdClass();
        $offset                     = $request->input('offset');
        $offset                     = isset($offset) ? $offset : 0;
        $limit                      = $request->input('limit');
        $limit                      = isset($limit) ? $limit : 10;
        $userId                     = isset($request['user_id']) ? $request['user_id'] : NULL ;
        $conditions                 = array();

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        $keyword                    = $request->input('keyword');
        $keyword                    = isset($keyword) ? $keyword : 0;


        $conditions['status']  = '1';
        $conditions['role_id']  = '2';
        $conditions['private_profile']   = 0;

        //$followerIds                = UserFollowing::where($conditions)->pluck('follower_id')->toArray();

        $userObj        = User::where($conditions)->where('id','!=',$userId);

        if(!empty($keyword)) {
            $userObj->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%'.$keyword.'%' )
                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%');
            });
        }

        $totalRecords   = $userObj->count();
        $userObj = $userObj
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();


        foreach ($userObj as $uObj) {
            $users[] = $this->_prepareUserResponse($uObj);
        }

        $responseArray['records']         = $users;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');

    }

    public function getProfile(Request $request)
    {
        $data                       = array();
        $emptyObj                   = new \stdClass();
        $userId                     = isset($request['user_id']) ? $request['user_id'] : NULL ;
        $profileId                  = isset($request['profile_id']) ? $request['profile_id'] : NULL ;
        $conditions                 = array();

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'user_id is required', false);
        }

        if (empty($profileId) || is_null($profileId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'profile_id is required', false);
        }

        $conditions['id']                   = $profileId;

        $profile = User::with(['badges' => function($query) {
            return $query->select('icon', 'name', 'badge_amount');
        }])->where($conditions)->first();

        $badges = $profile->badges;

        $postConditions['user_id']          = $profileId;
        $articles                           = Post::with(['publisher','tags'])->where($postConditions)
            ->orderBy('created_at', 'desc')->get();

        if ($profile) {

            $profile                        = $this->_prepareUserResponse($profile);
            $data['profile']                = $profile;
            $data['badges']                 = $badges;
            $data['articles']               = $articles;

            return RESTAPIHelper::response($data, 'Success', 'Data retrieved successfully');

        } else {

            return RESTAPIHelper::response($emptyObj, 'Error', 'wrong profile id', false);
        }

    }


    public function testPush(Request $request){

        $postData   = $request->all();
        $device     = '';

        $title      = 'thios is title';
        $msg        = 'This is test message for yum';
        $device     = $postData['device'];

        $apsArray   =  array('alert' => $title, 'message' => $msg, 'sound' => 'default');
        $data       = $this->SendPushNotificationAndroid($device,$apsArray);
        dd($data);
       // $apsArray =  array('alert' => $title, 'message' => $msg, 'sound' => 'default');
       // $this->SendPushNotification($device, $apsArray );

    }

    public function getUsers(Request $request)
    {
        $deviceType =  isset($request['device_type']) ? $request['device_type'] : NULL ;
        $keyword    =  isset($request['keyword'])     ? $request['keyword'] : NULL ;

        $conditions = array();
        if(!is_null($deviceType) && $deviceType != 'all') $conditions['device_type'] = $deviceType;

        $user = User::where($conditions);

        if(!empty($keyword)){

            $user->where(function ($query) use ($keyword) {
                $query->orwhere('first_name', 'like', '%' . $keyword . '%')
                    ->orwhere('last_name', 'like', '%' . $keyword . '%');
            });
        }
        $user = $user->where('device_token', '!=' , '')
            ->whereNotNull('device_token')->get();

        return RESTAPIHelper::response( $user );
    }

    public function getAllGroups(Request $request)
    {
        $keyword =  isset($request['keyword'])     ? $request['keyword'] : NULL ;

        $conditions = array();

        $user = Group::where('id','>',0);
        if(!empty($keyword) && !is_null($keyword))$user->where('name', 'LIKE' , '%'.$keyword.'%');
        $user = $user->get();
        return RESTAPIHelper::response( $user );
    }

    public function getAllUsers(Request $request)
    {
        $user = User::where('status', '=' , '1')->get();
        return RESTAPIHelper::response( $user );
    }

    public function changeStatus(Request $request,$userId)
    {


        $allNotificationsFromDB = User::where('id', $userId)->first();

        $currentStatus	=	$allNotificationsFromDB->status;
        if($currentStatus==0)
        {
            User::where('id', $userId)->update(['status' => 1]);
        }
        else
        {	User::where('id', $userId)->update(['status' => 0]);	}

        echo $currentStatus;

    }

    public function markUnmarkFeatured(Request $request)
    {
        $requestInfo            = $request->all();

        $user_id                = isset($requestInfo['user_id']) ? $requestInfo['user_id'] : 0;
        $is_featured            = isset($requestInfo['value']) ? $requestInfo['value'] : 0;

        $data['is_featured']    = $is_featured;
        $userInfo               = User::where('id', $user_id)->first();

        if($userInfo) { $userInfo->update($data); }

        return RESTAPIHelper::response('', 'Success', 'USer record has been updated successfully');
    }

    public function testPushIos(Request $request){

        $postData   = $request->all();
        $device     = '';

        $title      = 'thios is title';
        $msg        = 'This is test message for yum';
        $device     = $postData['device'];

        //$apsArray   =  array('alert' => $title, 'message' => $msg, 'sound' => 'default');
        //$data       = $this->SendPushNotificationAndroid($device,$apsArray);
        //dd($data);
         $apsArray =  array('alert' => $title, 'message' => $msg, 'sound' => 'default');
         $this->SendPushNotification($device, $apsArray );

    }


    public function deleteCacheFiles() {
        $path = base_path('storage/framework/views');
        $file=new Filesystem();
        $success = $file->cleanDirectory($path);
        print_r($success);
    }


}