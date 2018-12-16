<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;
use Gregwar\Image\Image;
use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Setting;
use App\User;
use App\Report;
use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Http\Requests\Frontend\EditProfileRequest;
use App\Helpers\RESTAPIHelper;
use App\Helpers\EmailHelper;
use Validator;
use App\Http\Requests\Frontend\UserRegisterRequest2;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApiController extends ApiBaseController {

    public function init() {
        return RESTAPIHelper::response([
                    'tutorial_video' => Setting::extract('app.link.tutorial_video', ''),
        ]);
    }

    public function getGuideBook() {
        return RESTAPIHelper::response([
                    'guidebook' => Setting::extract('app.link.guide_book', ''),
        ]);
    }

//public function register(UserRegisterRequest $request)
    public function register(UserRegisterRequest $request) {


        /*        $validator = Validator::make($request->all(), [
          'title' => 'required|max:255',
          'body' => 'required',
          ]);

          if ($validator->fails()) {
          echo "failed";
          } */



        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        $input['role_id'] = User::ROLE_MEMBER;

        if ($request->hasFile('profile_picture')) {
            $imageName = Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = public_path(Config::get('constants.front.dir.profilePicPath'));
            $request->file('profile_picture')->move($path, $imageName);

            if (Image::open($path . '/' . $imageName)->scaleResize(200, 200)->save($path . '/' . $imageName)) {
                $input['profile_picture'] = $imageName;
            }
        }

        $userCreated = User::create($input);

        // Send welcome email
        $emailBody = "Hello,

            Thank you for signing up on ValuationApp, enjoy your stay here.
            
            Thanks.";
        
        $emailSubject = 'Welcome on Board - ValuationApp';
        
        EmailHelper::sendMail($userCreated->email, $emailSubject, $emailBody);

        return $this->login($request);
    }

    public function login(Request $request) {
        $input = $request->only(['email', 'password']);
        $input['role_id'] = User::ROLE_MEMBER;

        if (!$token = JWTAuth::attempt($input)) {
            return RESTAPIHelper::response('Invalid credentials, please try-again.', false);
        }

        $userData = JWTAuth::toUser($token)->toArray();

        /* Do your additional/manual validation here like email verification or enable/disable */

        $result = [
            'user_id' => $userData['id'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'state' => $userData['state'],
            'phone' => $userData['phone'],
            'company_name' => $userData['company_name'],
            'profile_picture' => asset(Config::get('constants.front.dir.profilePicPath') . ($userData['profile_picture'] ?: Config::get('constants.front.default.profilePic'))),
            '_token' => $token,
                //'is_purchased'    => $userData['is_purchased'],
        ];

        return RESTAPIHelper::response($result);
    }

    public function resetPassword(Request $request) {
        $userRequested = User::where([
                    'email' => $request->get('email', ''),
                    'role_id' => User::ROLE_MEMBER,
                ])->first();

        if (!$userRequested)
            return RESTAPIHelper::response('Email not found in database.', false, 'invalid_email');

        $passwordGenerated = \Illuminate\Support\Str::random(12);

        $userRequested->password = Hash::make($passwordGenerated);
        $userRequested->save();

        // Send reset password email
        $emailBody = "You have requested to reset a password of your account, please find your new generated password below:
            
            New Password: " . $passwordGenerated . "
            
            Thanks.";
    
        $emailSubject = 'Reset Password - ValuationApp';
    
        EmailHelper::sendMail($userRequested->email, $emailSubject, $emailBody);

        return RESTAPIHelper::response('We have sent you new password in your email, please check your inbox as well as spam/junk folder.');
    }

    public function logout(Request $request) {
        JWTAuth::invalidate($this->extractToken());

        return RESTAPIHelper::emptyResponse();
    }

    public function viewMyProfile(Request $request) {
        $user = $this->getUserInstance();

        if (!$user)
            return RESTAPIHelper::response('Something went wrong here.', false);

        // Set default profile picture
        $user->profile_picture = asset(Config::get('constants.front.dir.profilePicPath') . ($user->profile_picture ?: Config::get('constants.front.default.profilePic')));

        return RESTAPIHelper::response(collect($user)->only([
                            'first_name',
                            'last_name',
                            'email',
                            'state',
                            'country',
                            'phone',
                            'company_name',
                            'profile_picture',
        ]));
    }

    public function updateMyProfile(EditProfileRequest $request) {
        $user = $this->getUserInstance();

        if (!$user)
            return RESTAPIHelper::response('Something went wrong here.', false);

        $dataToUpdate = array_filter([
            'first_name' => $request->get('first_name', null),
            'last_name' => $request->get('last_name', null),
            'email' => $request->get('email', null),
            'state' => $request->get('state', null),
            'country' => $request->get('country', null),
            'phone' => $request->get('phone', null),
            'company_name' => $request->get('company_name', null),
        ]);

        if ($request->has('password') && $request->get('password', '') !== '') {
            $dataToUpdate['password'] = \Hash::make($request->get('password'));
        }

        if ($request->hasFile('profile_picture')) {
            $imageName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = public_path(Config::get('constants.front.dir.profilePicPath'));
            $request->file('profile_picture')->move($path, $imageName);

            if (Image::open($path . '/' . $imageName)->scaleResize(200, 200)->save($path . '/' . $imageName)) {
                $dataToUpdate['profile_picture'] = $imageName;
            }
        }

        if (empty($dataToUpdate))
            return RESTAPIHelper::response('Nothing to update', false);


        $user->update($dataToUpdate);

        // Set default profile picture
        $user->profile_picture = asset(Config::get('constants.front.dir.profilePicPath') . ($user->profile_picture ?: Config::get('constants.front.default.profilePic')));

        return RESTAPIHelper::response(collect($user)->only([
                            'first_name',
                            'last_name',
                            'email',
                            'state',
                            'country',
                            'phone',
                            'company_name',
                            'profile_picture',
                            'is_purchased',
        ]));


        //return RESTAPIHelper::emptyResponse();
    }

    public function valuationReport(Request $request) {
        $input = $request->all();
        $user = $this->getUserInstance();

        if (!$user)
            return RESTAPIHelper::response('Something went wrong here.', false);


        $input['user_id'] = $user->id;

        //dd($user->first_name);

        $recordInserted = Report::create($input);

        if ($recordInserted->id) {
            return RESTAPIHelper::response('Record inserted successfully');
        } else {
            return RESTAPIHelper::response('Insertion error', false);
        }
    }

    public function inAppPurchased(Request $request) {
        $user = $this->getUserInstance();

        if (!$user)
            return RESTAPIHelper::response('Something went wrong here.', false);

        $dataToUpdate = array_filter([
            'is_purchased' => $request->get('is_purchased', null),
            'product_id' => $request->get('product_id', null),
        ]);

        if (empty($dataToUpdate))
            return RESTAPIHelper::response('Nothing to update', false);

        $user->update($dataToUpdate);

        // Send welcome email
        $emailBody = "Hello,

            Kindly follow the book using the link below.
    
            " . Setting::extract('app.link.guide_book', '') . "
    
            Please be acknowledged that the link is valid for 7 working days
    
            Thanks.";
    
    
        $emailSubject = 'Guide Link - ValuationApp';
    
        EmailHelper::sendMail($user->email, $emailSubject, $emailBody);

        return RESTAPIHelper::response('In app purchased successfully');
    }

    public function getUsers(Request $request) {

        $deviceType = isset($request['device_type']) ? $request['device_type'] : NULL;

        $conditions = array();
        if (!is_null($deviceType))
            $conditions['device_type'] = $deviceType;


        $user = User::where($conditions)->get();
        //  $user = User::all()->toArray();



        /* Do your additional/manual validation here like email verification or enable/disable */

        /*        $result   = [
          'user_id'         =>  $userData['id'],
          'first_name'      =>  $userData['first_name'],
          'last_name'       =>  $userData['last_name'],
          'email'           =>  $userData['email'],
          'state'           =>  $userData['state'],
          'phone'           =>  $userData['phone'],
          'company_name'    =>  $userData['company_name'],
          'profile_picture' =>  asset( Config::get('constants.front.dir.profilePicPath') . ($userData['profile_picture'] ?: Config::get('constants.front.default.profilePic')) ),
          '_token'          =>  $token,
          //'is_purchased'    => $userData['is_purchased'],
          ]; */

        return RESTAPIHelper::response($user);
    }

    ##Added irfan hammad

    public function socialLogin(Request $request) {
        $input = $request->all();
        extract($input);
        //$userId = $request->input('userId');

        header('Content-type: application/json');

        if (!isset($socialLoginId) || trim($socialLoginId) == '') {
            return RESTAPIHelper::response(array(), 'Error', 'Please Enter Device Token');
            /* $response=array("Response"=>"Error","Message"=>'Please Enter Device Token',"Result"=>array());
              echo json_encode($response);die(); */
        } elseif (!isset($socialPlatform) || trim($socialPlatform) == '') {
            return RESTAPIHelper::response(array(), 'Error', 'Please Enter Server Key');
            /* $response=array("Response"=>"Error","Message"=>'Please Enter Server Key',"Result"=>array());
              echo json_encode($response);die(); */
        } elseif (!isset($fullName) || trim($fullName) == '') {
            return RESTAPIHelper::response(array(), 'Error', 'Please Enter Message');
            /* $response=array("Response"=>"Error","Message"=>'Please Enter Message',"Result"=>array());
              echo json_encode($response);die(); */
        }


        $customClaims = ['social_media_id' => $socialLoginId, 'social_media_platform' => $socialPlatform];
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);
        $token = (array) $token;
        foreach ($token as $eachTokenValue) {
            $thisValue = $eachTokenValue;
        }

        $token = $thisValue;

        #echo $token['Tymon\JWTAuth\Tokenvalue'];die();
        $userData = DB::table('users')->where('social_media_id', $socialLoginId)->get();

        /* $customClaims = ['social_media_id' => $socialLoginId, 'social_media_platform' => $socialPlatform];
          $payload = JWTFactory::make($customClaims);

          $token = JWTAuth::encode($payload);

          print_r($token);die(); */

        if (!empty($userData)) {
            //Login
            $userData = (array) $userData[0];
            $result = [
                'user_id' => $userData['id'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'state' => $userData['state'],
                'phone' => $userData['phone'],
                'company_name' => $userData['company_name'],
                'profile_picture' => asset(Config::get('constants.front.dir.profilePicPath') . ($userData['profile_picture'] ?: Config::get('constants.front.default.profilePic'))),
                '_token' => $token,
                    //'is_purchased'    => $userData['is_purchased'],
            ];

            return RESTAPIHelper::response(array('User' => $result), 'Success', 'Logged In Successfully');
        } else {
            //register
            #$input             = $request->all();
            $NewArray = array();
            $NewArray['social_media_id'] = $socialLoginId;
            $NewArray['social_media_platform'] = $socialPlatform;
            $NewArray['full_name'] = $fullName;

            #$userCreated = User::create($NewArray);

            $userId = DB::table('users')->insertGetId($NewArray);

            $NewArray['id'] = $userId;

            $result = [
                'user_id' => $NewArray['id'],
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'state' => '',
                'phone' => '',
                'company_name' => '',
                'profile_picture' => '',
                '_token' => $token,
                    //'is_purchased'    => $userData['is_purchased'],
            ];

            return RESTAPIHelper::response(array('User' => $result), 'Success', 'User Created Successfully');
        }
    }

}
