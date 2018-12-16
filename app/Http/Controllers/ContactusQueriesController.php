<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\ContactusQueries;
use App\Http\Requests\Frontend\ContactusQueryRequest;

use App\Helpers\RESTAPIHelper;

use Validator;
use Illuminate\Support\Str;

class ContactusQueriesController extends ApiBaseController {


    public function _create(ContactusQueryRequest $request)
    {

        $input             = $request->all();
        $record            = ContactusQueries::create($input);

        return RESTAPIHelper::response( new \stdClass(),'Success','Your query has been submitted, Thank you.', false);

    }

}