<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Request;

// use JWTAuth;

trait JWTUserTrait {

    protected static $userInstance;

    /**
     * Extract token value from request
     *
     * @return string|Request
     */
    public static function extractToken($request = false) {

 
//        foreach (getallheaders() as $name => $value) {
//            echo "$name: $value\n";
//        }
//        die();
        if ($request && $request instanceof Request) {
            $token = $request->only('Token');
        } else if (is_string($request)) {
           // $token = $request;
        } else if (getallheaders()) {
            $headers[] = getallheaders();
            if (isset($headers[0]['Token'])) {
                $token = $headers[0]['Token'];
            }
        } else {
           // $token = Request::get('Token');
        }

        return $token;
    }

    /**
     * Return User instance or false if not exist in DB
     *
     * @return string|Request
     */
    public static function getUserInstance($request = false) {

        if (!self::$userInstance) {
            $token = self::extractToken($request);

            self::$userInstance = \JWTAuth::toUser($token);
        }

        return self::$userInstance;
    }

}
