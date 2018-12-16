<?php
namespace App\Http\Controllers;

use App\ExchangeProductIds;
use App\Setting;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\Faq;

use App\Amount;
use App\PostImage;
use App\ProductExchangeRequest;
use App\Helpers\RESTAPIHelper;

use Validator;
use App\Http\Requests\Frontend\AddReviewRequest;
use App\Http\Requests\Frontend\ExchangeProductRequest;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class FaqsController extends ApiBaseController {

    public function getHelpContent(Request $request) {

        $post                   = array();
        $postData               = $request->all();

      //  $conditions['key']   = $type;

        $dataObj                = Faq::orderBy('created_at', 'asc')
                                ->get();

        $responseArray       = $dataObj;

        return RESTAPIHelper::response($responseArray,'Success', 'Data retrieved successfully');
    }



    
}
