<?php
namespace App\Http\Controllers;

use App\UserSetting;
use Illuminate\Http\Request;
use Hash;
use Config;

use JWTAuth;
use Tymon\JWTAuth\Contracts;

use App\User;
use App\Report;
use App\FavouriteChef;

use App\Helpers\EmailHelper;

use App\Http\Requests\Frontend\UserRegisterRequest;
use App\Http\Requests\Frontend\SocialLoginRequest;
use App\Http\Requests\Frontend\ChangePasswordRequest;
use App\Helpers\RESTAPIHelper;

use Validator;
use Illuminate\Support\Facades\DB;

class UserFollowingController extends ApiBaseController {


    public function guestUserToken(Request $request)
    {
        $projectname            =  $request->input('project_name');
        $token                  =  base64_encode($projectname);
        $result['token']        =  $token;
        return RESTAPIHelper::response( $result);
    }

    public function register(UserRegisterRequest $request)
    {
        $input             = $request->all();

        if ($request->hasFile('profile_picture')) {
            $imageName =  \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = public_path(Config::get('constants.front.dir.profilePicUploadPath'));
            $request->file('profile_picture')->move($path, $imageName);
            $input['profile_picture'] = $imageName;
        }
		
        $input['password'] = Hash::make($input['password']);
        $input['role_id']  = User::ROLE_MEMBER;


        $userCreated = User::create($input);

        return $this->login($request,'1');
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




        if($is_register)
        {
            $dev_msg = "User has been successfully registered";
        }else{
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

        if ( !$userRequested )
            return RESTAPIHelper::response('Email not found in database.', false, 'invalid_email');

        $passwordGenerated = \Illuminate\Support\Str::random(12);

        $userRequested->password = Hash::make( $passwordGenerated );
        $userRequested->save();

        // Send reset password email
        $emailBody = "You have requested to reset a password of your account, please find your new generated password below:

            New Password: " . $passwordGenerated . "

            Thanks.";
        
        $emailSubject = 'Reset Password - Cloud Made Food';
    
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

        $input             = $request->all();
        $userId            = $input['user_id'];

        $user               = User::find($userId);

        if($user) {

            $dataToUpdate = array_filter([
                'first_name'         => $request->get('first_name', null),
                'last_name'          => $request->get('last_name', null),
                'full_name'          => $request->get('full_name', null),
                'country'            => $request->get('country', null),
                'city'               => $request->get('city', null),
                'phone'              => $request->get('phone', null),
                'dob'                => $request->get('dob', null),
                'field_of_work'      => $request->get('field_of_work', null),
                'headline_position'  => $request->get('headline_position', null),
                'previous_position'  => $request->get('previous_position', null)
            ]);


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

    public function changePassword(ChangePasswordRequest $request) {


        $input             = $request->all();
        $userId            = isset($input['user_id']) ? $input['user_id'] : 0;
        $oldPassword       = isset($input['old_password']) ? $input['old_password'] : 0;

        if(empty($userId)) {return RESTAPIHelper::response('user_id is required', false); }

        $is_authorized      = $this->checkTokenValidity($userId);
        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }

        $user               = User::find($userId);
        // dd($user);
        if($user) {


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


            $dataToUpdate = array_filter([
                'password' => $request->input('password', null)
            ]);

            if ($request->has('password') && $request->input('password', '') !== '') {
                $dataToUpdate['password'] = \Hash::make($request->input('password'));
            }

            if (empty($dataToUpdate)) {
                return RESTAPIHelper::response('','Error','Nothing to update', false);
            }

            $user->update($dataToUpdate);
        } else {

            return RESTAPIHelper::response('Wrong user id provided', false);
        }

        return RESTAPIHelper::response( new \stdClass(),'Success','Password changed successfully', false);

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


        //dd($userData);
        $data['id']                         = $userData['id'];
        $data['user_id']                    = $userData['id'];
        $data['first_name']                 = $userData['first_name'];
        $data['last_name']                  = $userData['last_name'];
        $data['email']                      = $userData['email'];
        $data['phone']                      = $userData['phone'];
        $data['country']                    = $userData['country'];
        $data['city']                       = $userData['city'];
       // $data['latitude']                   = $userData['latitude'];
       // $data['longitude']                  = $userData['longitude'];
        $data['status']                     = $userData['status'];

        $data['profile_image']              = $userData['profile_image'];
        $data['handle']                     = $userData['handle'];
        $data['field_of_work']              = $userData['field_of_work'];
        $data['headline_position']          = $userData['headline_position'];
        $data['previous_position']          = $userData['previous_position'];
        $data['social_media_id']            = $userData['social_media_id'];
        $data['social_media_platform']      = $userData['social_media_platform'];



        if(!empty($token)) $data['token']   = $token;
        return $data;
    }

    public function getChefs(Request $request) {

        $offset = $request->input('offset');
        $offset = isset($offset) ? $offset : 0;
        $limit  = $request->input('limit');
        $limit  = isset($limit) ? $limit : 0;

        $userId  = $request->input('user_id');
        $userId  = isset($userId) ? $userId : 0;


        $latitude    = $request->input('latitude');
        $latitude  = isset($latitude) ? $latitude : 0;
        $longitude   = $request->input('longitude');
        // .......
        $favourites  = $request->input('favourites');
        $favourites  = isset($favourites) ? $favourites : 0;

        $top_chef  = $request->input('top_chef');
        $top_chef  = isset($top_chef) ? $top_chef : 0;

        $chefIds = $chefIdsLocationWise  =array();

        if(isset($latitude) && isset($longitude) &&  !empty($latitude)) {

            $chefIdsArr = DB::select("SELECT `id`, (3959 * ACOS( COS( RADIANS( " . $latitude . " ) ) * COS( RADIANS( `latitude` ) ) * COS(RADIANS( `longitude` )
                            - RADIANS( " . $longitude . " )) + SIN(RADIANS(" . $latitude . ")) * SIN(RADIANS(`latitude`)))) `distance` FROM users
                            HAVING `distance` < 100
                            ORDER BY distance ASC");

            if($chefIdsArr){
                foreach($chefIdsArr as $uObj) {
                    $chefIdsLocationWise[] = $uObj->id;
                }
            }
        }


        if($favourites ) {
            $conditionForFavouriteChefs['user_id'] = $userId;
            $chefIds = FavouriteChef::where($conditionForFavouriteChefs)
                ->groupBy('chef_id')
                ->pluck('chef_id')
                ->toArray();
        }


        // If both filters apply together ... Currently not a case - out of scope
        if(!empty($favourites) && !empty($latitude)  ) {

            $chefIds = array_intersect($chefIds,$chefIdsLocationWise);
        }


        $chefs      = array();

        if( $userId == 0 ) {return RESTAPIHelper::response('','Error','user id is required', false); }

//        $is_authorized = $this->checkTokenValidity($userId);
//        if ($is_authorized == 0) {
//            return RESTAPIHelper::response('', 'Error', 'Invalid Token or User Id', false);
//        }

        $conditions['is_chef'] = 1;

        $userObj      = User::where('id', '<>', $userId)->where($conditions);

        // Filter to Get Favourite Chef ..............
        if($favourites) {  $userObj      = $userObj->whereIn('id',$chefIds); }
        // Filter to Get Favourite Chef Ends here ..............

        // Filter to Get Near By Chefs ..............
        if($latitude) {  $userObj      = $userObj->whereIn('id',$chefIdsLocationWise); }
        // Filter to Get Near By Chef Ends here ..............

        $totalRecords = $userObj->count();

        if($top_chef == 1){
            //$userObj->sortBy('portion_sold');
           // $userObj = $userObj->orderBy('portion_sold', 'desc');
        } else {
            $userObj = $userObj->orderBy('created_at', 'desc');
        }

        if($top_chef == 1){
            $userObj->get()->sortByDesc('portion_sold');
            // $userObj = $userObj->orderBy('portion_sold', 'desc');
        }


        if(!empty($limit))$userObj = $userObj->offset($offset)->limit($limit);
        $userObj = $userObj->get()->sortByDesc('portion_sold');


        foreach ($userObj as $uObj) {
            $chefs[] =   $this->_prepareChefResponse($uObj, $request);
        }

        $responseArray['Chefs']         = $chefs;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    public function markFavouriteUnfavourite(Request $request) {

        $emptyObj   = new \StdClass();
        $postData   = $request->all();
        $userId     = $request->input('user_id');
        $userId     = isset($userId) ? $userId : 0;

        $chefId     = $request->input('chef_id');
        $chefId     = isset($chefId) ? $chefId : 0;


        if (empty($chefId) || is_null($chefId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'Invalid Chef Id', false);
        }

        if (empty($userId) || is_null($userId)) {
            return RESTAPIHelper::response($emptyObj, 'Error', 'Invalid User Id', false);
        }



        $conditions['user_id']  = $userId;
        $conditions['chef_id']  = $chefId;
        $is_favourite           = FavouriteChef::where($conditions)->count();


        if ($is_favourite > 0) { // removing from favourite list ..
            $favPostObj = FavouriteChef::where($conditions)->first();
            //dd($favPostObj);
            $favPostObj->delete();
            return RESTAPIHelper::response($emptyObj, 'Success', 'Removed from favourite list');
        } else { // adding to favourite list ..
            FavouriteChef::create($postData);
            return RESTAPIHelper::response($emptyObj, 'Success', 'Added to user favourite successfully');
        }
    }

    public function _prepareChefResponse($userData , $request) {

        $data['id']                         = $userData['id'];
        $data['user_id']                    = $userData['id'];
        $data['full_name']                  = $userData['full_name'];
        $data['email']                      = $userData['email'];
        $data['mobile_no']                  = $userData['mobile_no'];
        $data['address']                    = $userData['address'];
        $data['latitude']                   = $userData['latitude'];
        $data['longitude']                  = $userData['longitude'];
        $data['status']                     = $userData['status'];
        $data['profile_image']              = $userData['profile_image'];
        $data['cover_image']                = $userData['cover_image'];
        $data['biography']                  = $userData['biography'];
        $data['is_verified']                = $userData['is_verified'];
        $data['verification_code']          = $userData['verification_code'];
        $data['is_chef']                    = $userData['is_chef'];
        $data['is_favourite']                    = $userData['is_favourite'];
        $data['portion_sold']               = $userData['portion_sold'];
        $data['price_earned']               = $userData['price_earned'];
        $data['order_count']                = $userData['order_count'];

        return $data;
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

        $conditions = array();
        if(!is_null($deviceType) && $deviceType != 'all') $conditions['device_type'] = $deviceType;



        $user = User::where($conditions)->where('device_token', '!=' , '')->whereNotNull('device_token')->get();
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
                ];*/

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
    
}