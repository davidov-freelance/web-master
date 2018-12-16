<?php

namespace App\Http\Controllers\Backend;

use Config;
use Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Backend\AdminRequest;
use App\Http\Requests\Backend\ChangedPasswordRequest;

use App\Admin;
use App\User;
use App\Log;
use App\FieldOfWork;

class AdminController extends BackendController
{
    public function getIndex()
    {
        $admins = Admin::where(['role_id' => 1])->where('is_subadmin' , '1')->get();

        //dd($admins );
        #
        #$admins =	DB::table('users')->where('role_id', 3)->where('userType','subadmin')->get();

        return backend_view( 'admins.index', compact('admins') );
    }

    public function edit(Admin $admin)
    {
        /*if ( !$admin->isAdmin() )
            abort(404);*/

        $dummy = array('0'=>'Select Field Of Work');
        $fields = FieldOfWork::where('status',1)->pluck('title','id')->toArray();
        $fields = array_merge($dummy,$fields);

        return backend_view( 'admins.edit', compact('admin','fields') );
    }

    public function add()
    {
        $dummy = array('0'=>'Select Field Of Work');
        $fields = FieldOfWork::where('status',1)->pluck('title','id')->toArray();
        $fields = array_merge($dummy,$fields);
        return backend_view( 'admins.add', compact('fields')  );
    }

    public function create(AdminRequest $request,Admin $admin)
    {
        $postData = $request->all();

        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $postData['password'] = \Hash::make( $postData['password'] );
        }

        $postData['role_id'] = 1;
        $postData['is_subadmin'] = '1';

        if($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture') ;
            $fileName =  \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/' ;
            $file->move($destinationPath,$fileName);
            $postData['profile_picture']= $fileName;
        }

        $admin->create( $postData );

        session()->flash('alert-success', 'Admin has been created successfully!');
        return redirect( 'backend/admin/add/' . $admin->id );

    }

    public function update(AdminRequest $request, Admin $admin)
    {
        /*if ( $admin->isAdmin() )
            abort(404);*/

        $postData = $request->all();

        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $postData['password'] = \Hash::make( $postData['password'] );
        }

        if($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture') ;
            $fileName =  \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $destinationPath = public_path().'/images/' ;
            $file->move($destinationPath,$fileName);
            $postData['profile_picture']= $fileName;
        }
        
        $admin->update( $postData );

        session()->flash('alert-success', 'Admin has been updated successfully!');
        return redirect('backend/admin');
    }

    public function destroy(Admin $admin)
    {
        /* if ( $admin->isAdmin() )
             abort(404);*/

        $admin->delete();

        session()->flash('alert-success', 'Admin has been deleted successfully!');
        return redirect( 'backend/admin' );
    }

    public function profile($id)
    {
        $admins        = Admin::where(['role_id' => Admin::ROLE_MEMBER])->where(['id' => $id])->first()->toArray();
        $adminLogs     = Log::with(['admin'])->where(['log_generator' => $id])->get()->toArray();

//dd($adminLogs);
        return backend_view( 'admins.profile', compact('admins' ,'adminLogs') );
    }

    public function changePasswordForm($id)
    {

        if($this->isMyOwnProfile($id) == 0) abort(403, 'Unauthorized action.');

        $user        = Admin::where(['role_id' => Admin::ROLE_ADMIN])->where(['id' => $id])->first();
        return backend_view( 'admins.changepassword',compact('user') );
    }

    public function updatePassword(ChangedPasswordRequest $request, Admin $user)
    {

        $old_password = "";
        $postData = $request->all();

        if ($request->has('old_password') && $request->get('old_password', '') != '') {
            //$old_password = \Hash::make($postData['password1']);
            $attempt = Auth::guard()->attempt(['email' => $user->email, 'password' => $postData['old_password']]);

            if ($attempt) {


                if ($request->has('password') && $request->get('password', '') != '') {
                    $postData['password'] = \Hash::make($postData['password']);
                }

                // dd($request);
                $user->update($postData);

                session()->flash('alert-success', 'Password has been updated successfully!');
                return redirect('backend/admin/change-password/' . $user->id);
            }
            else {
                session()->flash('alert-danger', 'Incorrect old password.');
                return redirect('backend/admin/change-password/' . $user->id);
            }
        }
    }

    public function changeToUser(Request $request)
    {
        $userId = $request::input('user_id');

        $user = User::find($userId);

        if (!$user) {
            return redirect('backend/admin');
        }

        $user->role_id = User::ROLE_MEMBER;
        $user->admin_role = Admin::ADMIN_ROLE_MODERATOR;
        $user->is_subadmin = 0;

        if ($user->save()) {
            return redirect('backend/user/edit/' . $userId);
        }

        return back();
    }
}
