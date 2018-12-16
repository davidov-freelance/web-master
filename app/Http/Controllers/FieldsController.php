<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;
use Gregwar\Image\Image;
use JWTAuth;
use App\User;
use App\FieldOfWork;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Helpers\RESTAPIHelper;
use Validator;


class FieldsController extends ApiBaseController {

    public function getFields(Request $request) {

        $input              = $request->all();
        $userId             = isset($input['user_id']) ? $input['user_id'] : 0;
        $status             = isset($input['status']) ? $input['status'] : 1;


        $fields['Fields']       = FieldOfWork::where('status',1)->get();
        
        return RESTAPIHelper::Response($fields);
    }


}
