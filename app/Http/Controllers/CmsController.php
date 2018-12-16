<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;
use App\Helpers\RESTAPIHelper;

use Validator;
use Illuminate\Support\Str;

use App\Cms;


class CmsController extends ApiBaseController {

    public function getCmsPage(Request $request) {

        $emptyObj                      = new \stdClass();
        $input                         = $request->all();

        if (!isset($input['key']) || trim($input['key']) == '') {
            return RESTAPIHelper::response(array(), 'Error', 'key is required parameter');
        }

        $data['key']                   = $request->input('key');
        $data['user_id']               = $request->input('user_id');

        $condition['key']              = $data['key'];
        $pageObject                    = Cms::where($condition)->first();


        if($pageObject) {
            $pageObject->_description = strip_tags($pageObject->body);
            return RESTAPIHelper::response($pageObject, 'Success', 'Data retrieved successfully', false);
        } else {
            return RESTAPIHelper::response($emptyObj,'Error','wrong key supplied', false);
        }
    }

    
}