<?php

namespace App\Http\Controllers\Backend;

use Config;

use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\UserRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
//use Validator;

use App\User;
use App\Vehicle;
use App\VehicleImage;
use App\VehicleRequest;
use App\VehicleBidding;
use App\VehicleWatchList;
use App\Log;

class SubAdminsController extends BackendController
{
    public function getIndex()
    {
        $users = User::where(['role_id' => User::ROLE_MEMBER])->where(['is_subadmin' => 1])->get();
        return backend_view( 'subadmin.index', compact('users') );
    }

    public function edit(User $user)
    {
        /*if ( !$user->isAdmin() )
            abort(404);*/

        return backend_view( 'users.edit', compact('user') );
    }

    public function add()
    {
            return backend_view( 'users.add' );
    }

    public function create(UserRequest $request,User $user)
    {
        $postData = $request->all();

        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $postData['password'] = \Hash::make( $postData['password'] );
        }

        $postData['role_id'] = User::ROLE_MEMBER;



        $user->create( $postData );

        session()->flash('alert-success', 'User has been created successfully!');
        return redirect( 'backend/user/add/' . $user->id );

    }

    public function update(UserRequest $request, User $user)
    {
        if ( $user->isAdmin() )
            abort(404);

        $postData = $request->all();

        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $postData['password'] = \Hash::make( $postData['password'] );
        }


        if($file = $request->hasFile('profile_picture')) {

            $file = $request->file('profile_picture') ;


            $fileName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            echo $destinationPath = public_path().'/images/' ;
            $file->move($destinationPath,$fileName);
           echo $user->profile_picture = $fileName ;
        }

       // dd($request);
        $user->update( $postData );

        session()->flash('alert-success', 'User has been updated successfully!');
        return redirect( 'backend/user/edit/' . $user->id );
    }

    public function destroy(User $user)
    {
        if ( $user->isAdmin() )
            abort(404);

        $user->delete();

        session()->flash('alert-success', 'User has been deleted successfully!');
        return redirect( 'backend/user' );
    }

    public function profile($id)
    {

       $users        = User::where(['id' => $id])->first()->toArray();
       $vehiclesRequest = VehicleRequest::with('vehicleInfo')->with('vehicleImages')->where(['user_id' => $id])->get()->toArray();
       $vehiclesBidding = VehicleBidding::with('vehicleInfo')->with('vehicleImages')->where(['user_id' => $id])->get()->toArray();

       return backend_view( 'users.profile', compact('users','vehiclesRequest','vehiclesBidding' ) );

    }
	
	public function changeStatus(Request $request,$userId)
    {

		
	   $allNotificationsFromDB = User::where('id', $userId)->first();
	   
	   $currentStatus	=	$allNotificationsFromDB->status;	   
	   if($currentStatus==0)
	   {
	   		User::where('id', $userId)->update(['status' => 1]);
	   }
	   else
	   {	User::where('id', $userId)->update(['status' => 0]);	}

        echo $currentStatus;
	   
     }
}
