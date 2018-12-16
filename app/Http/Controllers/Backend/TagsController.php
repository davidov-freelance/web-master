<?php

namespace App\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests\Backend\TagCreateRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;

use App\Tag;
use Illuminate\Support\Facades\Auth; // to get admin info/ authenticated user


class TagsController extends BackendController {

    public function getIndex() {

        $tags = Tag::get();
        return backend_view('tags.listing', compact('tags'));
    }

    public function edit(Tag $tag) {

        return backend_view('tags.edit', compact('tag'));
    }

    public function add() {

        return backend_view('tags.add');
    }
    
    public function create(TagCreateRequest $request) {

        $postData   = $request->all();
        $id         = Tag::create($postData)->id;
        session()->flash('alert-success', 'Tag has been added successfully!');
        return redirect('backend/tags');
    }

    public function update(TagCreateRequest $request, Tag $tag) {

        $postData 		 = $request->all();
        $tag->update($postData);
        session()->flash('alert-success', 'Record has been updated successfully!');
        return redirect('backend/tags/edit/' . $tag->id);
    }

    public function destroy(Tag $tag) {

        $tag->delete();
        session()->flash('alert-success', 'Tag has been deleted successfully!');
        return redirect('backend/tags');
    }

}
