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
use App\Tag;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Helpers\RESTAPIHelper;
use Validator;


class TagsController extends ApiBaseController {

    public function getTags(Request $request) {

        $input              = $request->all();
        $userId             = isset($input['user_id']) ? $input['user_id'] : 0;
        $keyword            = isset($input['keyword']) ? $input['keyword'] : 0;


        $tags['Tags']       = Tag::get();
        
        return RESTAPIHelper::Response($tags);
    }


}
