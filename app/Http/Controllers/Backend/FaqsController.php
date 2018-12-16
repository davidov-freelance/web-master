<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests\Backend\CmsRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\Auth;
//use Validator;

use App\Faq;

class FaqsController extends BackendController
{
    public function getIndex()
    {
        $help_content           = Faq::where('key','faq')->get();
        return backend_view( 'faq.index', compact('help_content') );
    }

    public function edit($type)
    {
       // dd($type);
        $content = Faq::where('key',$type)->get();
        return backend_view( 'faq.edit', compact('content') );
    }


    public function update(Request $request)
    {
        $postData       = $request->all();
        $totalHeading   = count($postData['heading']);
        $type           = $postData['type'];

        Faq::where('key',$type)->delete();
        if($totalHeading >  0) {

            $data['key']     = $type;
            for($i=0;$i<$totalHeading;$i++){

                if(!empty($postData['heading'][$i])) {
                    $data['heading']     = $postData['heading'][$i];
                    $data['description'] = $postData['description'][$i];
                    Faq::create($data);
                }
            }
        }

        session()->flash('alert-success', 'Record has been updated successfully!');
        return redirect( 'backend/faq/');
    }

}
