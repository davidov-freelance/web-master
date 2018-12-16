<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Config;

use JWTAuth;

use App\User;
use App\Log;

use App\Helpers\RESTAPIHelper;
use App\Helpers\EmailHelper;

use Validator;
use App\Http\Requests\Frontend\UserRegisterRequest2;


class LogsController extends ApiBaseController
{
    
    public function getLogs(Request $request)
    {
        $logs = Log::all();
        return RESTAPIHelper::response($logs);
    }
    
    public function login(Request $request)
    {
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
        ];
        
        return RESTAPIHelper::response($result);
    }
    
    public function resetPassword(Request $request)
    {
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
    
    public function logout(Request $request)
    {
        JWTAuth::invalidate($this->extractToken());
        
        return RESTAPIHelper::emptyResponse();
    }
    
    
}