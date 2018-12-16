<?php

namespace App\Http\Controllers\Backend;


use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
// use App\Http\Requests;
use App\Http\Requests\Backend\CategoryCreateRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;


use App\User;
use App\Category;
use App\Post;
use Illuminate\Support\Facades\Auth; // to get admin info/ authenticated user


class CategoriesController extends BackendController {

    public function getIndex() {

        $categories = Category::get();
        //dd($categories);
        return backend_view('categories.listing', compact('categories'));
    }



    public function edit(Category $category) {
        /* if ( !$user->isAdmin() )
          abort(404); */
        

        return backend_view('categories.edit', compact('category'));
    }

    public function add() {

        return backend_view('categories.add');
    }
    
    public function create(CategoryCreateRequest $request) {

        $postData   = $request->all();

        if($file = $request->hasFile('image')) {
            $file = $request->file('image') ;

            $fileName        =  \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/categories/' ;
            $file->move($destinationPath,$fileName);
            $postData['image'] = $fileName ;
        }

        if($file = $request->hasFile('selected_image')) {
            $file = $request->file('selected_image') ;

            $fileName        =  \Illuminate\Support\Str::random(12) . '.' . $request->file('selected_image')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/categories/' ;
            $file->move($destinationPath,$fileName);
            $postData['selected_image'] = $fileName ;
        }

        $id         = Category::create($postData)->id;

        session()->flash('alert-success', 'Category has been added successfully!');
        return redirect('backend/categories/add/');
    }

    public function update(CategoryCreateRequest $request, Category $category) {

        $id 	  		 = $category->id;
        $postData 		 = $request->all();

        if($file = $request->hasFile('image')) {
            $file = $request->file('image') ;

            $fileName        =  \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/categories/' ;
            $file->move($destinationPath,$fileName);
            $postData['image'] = $fileName ;
        }

        if($file = $request->hasFile('selected_image')) {
            $file = $request->file('selected_image') ;

            $fileName        =  \Illuminate\Support\Str::random(12) . '.' . $request->file('selected_image')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/categories/' ;
            $file->move($destinationPath,$fileName);
            $postData['selected_image'] = $fileName ;
        }

        $category->update($postData);

        session()->flash('alert-success', 'Record has been updated successfully!');
        return redirect('backend/categories/edit/' . $category->id);
    }

    public function destroy(Category $category) {

        $category->delete();

        session()->flash('alert-success', 'Category has been deleted successfully!');
        return redirect('backend/categories');
    }

}
