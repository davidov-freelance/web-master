<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\CmsRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\Auth;
//use Validator;

use App\Cms;

class CmsController extends BackendController
{
    public function getIndex()
    {
        $pages = Cms::all();

        return backend_view( 'cms.index', compact('pages') );
    }

    public function edit($page_id)
    {
        $cms = CMS::find($page_id);
        return backend_view( 'cms.edit', compact('cms') );
    }

    public function add()
    {
            return backend_view( 'cms.add' );
    }

    public function create(CmsRequest $request)
    {

        $user = Auth::user();


        $postData = $request->all();

        Cms::create( $postData );

        session()->flash('alert-success', 'Cms Page has been created successfully!');
        return redirect( 'backend/cms/');

    }

    public function update(CmsRequest $request ,$page_id )
    {

        $cms = CMS::find($page_id);
        $postData = $request->all();


        $cms->update( $postData );

        session()->flash('alert-success', 'Page has been updated successfully!');
        return redirect( 'backend/cms/');
    }

    public function destroy($page_id)
    {
        Cms::destroy($page_id);
        session()->flash('alert-success', 'Page has been deleted successfully!');
        return redirect( 'backend/cms' );
    }
}
