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
use App\Category;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Helpers\RESTAPIHelper;
use Validator;


class CategoriesController extends ApiBaseController {



    public function getAllCategories(Request $request) {

        $input              = $request->all();
        $userId             = isset($input['user_id']) ? $input['user_id'] : 0;

//        $is_authorized      = $this->checkTokenValidity($userId);
//        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }

        $conditions['status']           = 1;
        $conditions['parent_id']        = isset($input['category_id']) ? $input['category_id'] : 0 ;
        $parentCategories               = Category::where($conditions)->get();
        if($parentCategories) {


            $parentCategories =$parentCategories->toArray();
            //dd($parentCategories);
            foreach($parentCategories as $pCid ) {

                $subCategories               = Category::where('parent_id',$pCid['id'])->get();
                $pCid['SubCategories']       = $subCategories;

                $categories[] = $pCid;
            }

        }

        return RESTAPIHelper::Response($categories);
    }

    public function getCategories(Request $request) {

        $input              = $request->all();
        $userId             = isset($input['user_id']) ? $input['user_id'] : 0;

//        $is_authorized      = $this->checkTokenValidity($userId);
//        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }

        $conditions['status']           = 1;
        //$conditions['parent_id']        = isset($input['category_id']) ? $input['category_id'] : 0 ;
        $categories['Categories']       = Category::where($conditions)->orderBy('sort_order','ASC')->get();

        //dd($categories);
        return RESTAPIHelper::Response($categories);
    }


}
