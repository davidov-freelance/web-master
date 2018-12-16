<?php
namespace App\Http\Controllers;

use App\ReportPost;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\ContactusQueries;
use App\Http\Requests\Frontend\ReportPostRequest;

use App\Helpers\RESTAPIHelper;

use Validator;
use Illuminate\Support\Str;

class ReportPostController extends ApiBaseController {


    public function _create(ReportPostRequest $request)
    {

        $input             = $request->all();
        $record            = ReportPost::create($input);

        return RESTAPIHelper::response( new \stdClass(),'Success','Your report has been submitted, Thank you.', false);

    }

    public function getReportOption(Request $request)
    {

        $reportReason = array('Abusive Post', 'Inappropriate Content', 'Other reason');

        return RESTAPIHelper::response( $reportReason,'Success','Record retrieved', false);

    }

}