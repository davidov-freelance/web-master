<?php

namespace App\Http\Controllers\Backend;


use App\Comment;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
// use App\Http\Requests;
use App\Http\Requests\Backend\PostCreateRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
//use Validator;

use App\User;
use App\Post;
use App\ContactusQueries;
use App\Conversation;
use App\ConversationThread;
use Illuminate\Support\Facades\Auth; // to get admin info/ authenticated user


class ContactUsController extends BackendController {

    public function getIndex()
    {
        $list = ContactusQueries::with('user')->get();
        return backend_view( 'contacts.index', compact('list') );
    }


    public function destroy(ContactusQueries $contact) {

        $contact->Delete();

        session()->flash('alert-success', 'Contact has been deleted successfully!');
        return redirect('backend/contacts');
    }

}
