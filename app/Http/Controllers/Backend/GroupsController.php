<?php

namespace App\Http\Controllers\Backend;

use App\Group;
use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\GroupCreateRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\Auth;
//use Validator;

use App\GroupMember;
use App\User;

class GroupsController extends BackendController
{

    public function getIndex() {

        $groups = Group::with('member.user')->get();
       // dd($groups);
        return backend_view('groups.index', compact('groups'));
    }

    public function add()
    {
        return backend_view( 'groups.add-2' );
    }

    public function edit(Group $group)
    {
        /*if ( !$user->isAdmin() )
            abort(404);*/

        $members = GroupMember::where('group_id',$group->id)->pluck('user_id')->toArray();

        return backend_view( 'groups.edit', compact('group','members') );
    }

    public function create(GroupCreateRequest $request)
    {
        $postData = $request->all();
        $name   = isset($postData['name']) ? $postData['name'] : 'title' ;


        if(empty($postData['uids'])) {

            session()->flash('alert-warning', 'Select at least one user');
            return redirect( 'backend/group/new/');
        }

        $users = User::find($postData['uids']);
        $groupId = Group::create($postData)->id;

        $data['group_id'] = $groupId;
        foreach($users as $user) {

            $data['user_id'] = $user->id;
            GroupMember::create($data);

        }

        session()->flash('alert-success', 'Group has been created successfully!');
        return redirect( 'backend/groups');
    }


    public function update(GroupCreateRequest $request, Group $group)
    {
        $postData   = $request->all();
        $name       = isset($postData['name']) ? $postData['name'] : 'title' ;

        if(empty($postData['uids'])) {

            session()->flash('alert-warning', 'Select at least one user');
            return redirect( 'backend/group/edit/'.$group->id.'');
        }

        $group->update($postData);
        $data['group_id'] = $group->id;

        GroupMember::where('group_id',$data['group_id'])->delete();
        $users = User::find($postData['uids']);

        foreach($users as $user) {
            $data['user_id'] = $user->id;
            GroupMember::create($data);
        }

        session()->flash('alert-success', 'Group has been updated successfully!');
        return redirect( 'backend/groups');
    }

    public function destroy(Group $group) {

        $group->delete();
        session()->flash('alert-success', 'Group has been deleted successfully!');
        return redirect('backend/groups');
    }

}
