<?php

namespace App\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests\Backend\FieldCreateRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;

use App\FieldOfWork;
use Illuminate\Support\Facades\Auth; // to get admin info/ authenticated user


class FieldsController extends BackendController {

    public function getIndex() {

        $fields = FieldOfWork::get();
        return backend_view('fields.listing', compact('fields'));
    }

    public function edit(FieldOfWork $field) {

        return backend_view('fields.edit', compact('field'));
    }

    public function add() {

        return backend_view('fields.add');
    }
    
    public function create(FieldCreateRequest $request) {

        $postData   = $request->all();
        $id         = FieldOfWork::create($postData)->id;
        session()->flash('alert-success', 'Record has been added successfully!');
        return redirect('backend/fields');
    }

    public function update(FieldCreateRequest $request, FieldOfWork $field) {

        $postData 		 = $request->all();
        $field->update($postData);
        session()->flash('alert-success', 'Record has been updated successfully!');
        return redirect('backend/fields/edit/' . $field->id);
    }

    public function destroy(FieldOfWork $field) {

        $field->delete();
        session()->flash('alert-success', 'Record has been deleted successfully!');
        return redirect('backend/fields');
    }

}
