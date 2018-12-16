<?php

namespace App\Http\Controllers\Backend;


use App\FieldOfWork;
use Config;
//use Illuminate\Http\Request;
use Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Backend\UserRequest;
use App\UserBadge;
use App\Badge;

use App\User;
use App\Admin;

class UserController extends BackendController
{
    public function getIndex()
    {
        $users = User::with('badges')->where(['role_id' => User::ROLE_MEMBER])->get();
        return backend_view('users.index', compact('users'));
    }

    public function add()
    {
        $dummy = array('0' => 'Select Field Of Work');
        $fields = FieldOfWork::where('status', 1)->pluck('title', 'id')->toArray();
        $fields = array_merge($dummy, $fields);
        $badgeIcons = $this->_collectBadgeIcons();
        return backend_view('users.add', compact('fields', 'badgeIcons'));
    }

    public function edit(User $user)
    {
        /*if ( !$user->isAdmin() )
            abort(404);*/

//        $iam = Auth::user();
//
//        exit('<pre>' . print_r($iam, 1) . '</pre>');

        $dummy = array('0' => 'Select Field Of Work');
        $fields = FieldOfWork::where('status', 1)->pluck('title', 'id')->toArray();
        $fields = array_merge($dummy, $fields);

        $badgeIcons = $this->_collectBadgeIcons();
        $userBadges = UserBadge::where('user_id', '=', $user->id)->get();

        return backend_view('users.edit', compact('user', 'fields', 'userBadges', 'badgeIcons'));
    }

    public function create(UserRequest $request, User $user)
    {
        $postData = $request->all();

        if ($request->has('password') && $request->get('password', '') != '') {
            $postData['password'] = \Hash::make($postData['password']);
        }

        $postData['role_id'] = User::ROLE_MEMBER;


        if ($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/';
            $file->move($destinationPath, $fileName);
            $postData['profile_picture'] = $fileName;
        }

        $createdUser = $user->create($postData);

        if (!empty($postData['badges'])) {
            $this->_addNewBadges($postData['badges'], $createdUser->id);
        }

        session()->flash('alert-success', 'User has been created successfully!');
        return redirect('backend/user/add/' . $user->id);

    }

    public function update(UserRequest $request, User $user)
    {
        if ($user->isAdmin())
            abort(404);

        $postData = $request->all();

        if ($request->has('password') && $request->get('password', '') != '') {
            $postData['password'] = \Hash::make($postData['password']);
        }


        if ($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/';
            $file->move($destinationPath, $fileName);
            $postData['profile_picture'] = $fileName;
        }

        if ($file = $request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $fileName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('cover_photo')->getClientOriginalExtension();
            $destinationPath = public_path() . '/images/';
            $file->move($destinationPath, $fileName);
            $postData['cover_photo'] = $fileName;
        }

        // dd($request);
        $user->update($postData);

        if (!empty($postData['badges'])) {
            $this->_updateUserBadges($postData['badges'], $user->id);
        }

        session()->flash('alert-success', 'User has been updated successfully!');
        return redirect('backend/user');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin())
            abort(404);

        $user->delete();

        session()->flash('alert-success', 'User has been deleted successfully!');
        return redirect('backend/user');
    }

    public function profile($id)
    {
        $users = User::with(['badges' => function($query) {
            return $query->select('icon', 'name', 'badge_amount');
        }])->where(['id' => $id])->first();
        return backend_view('users.profile', compact('users'));
    }

    public function changeStatus(Request $request, $userId)
    {
        $allNotificationsFromDB = User::where('id', $userId)->first();

        $currentStatus = $allNotificationsFromDB->status;
        if ($currentStatus == 0) {
            User::where('id', $userId)->update(['status' => 1]);
        } else {
            User::where('id', $userId)->update(['status' => 0]);
        }

        echo $currentStatus;
    }

    public function changeToAdmin(Request $request)
    {
        $userId = $request::input('user_id');

        $user = User::find($userId);

        if (!$user) {
            return redirect('backend/user');
        }

        $user->role_id = User::ROLE_ADMIN;
        $user->admin_role = Admin::ADMIN_ROLE_MODERATOR;
        $user->is_subadmin = 1;

        if ($user->save()) {
            return redirect('backend/admin/edit/' . $userId);
        }

        return back();
    }

    private function _collectBadgeIcons()
    {
        $badges = Badge::get();

        $badgeIconsData = [];
        foreach ($badges as $badge) {
            $badgeIconsData[] = [
                'iconFilePath' => $badge->badge_icon,
                'iconValue' => $badge->id,
                'iconTitle' => $badge->name,
            ];
        }
        return $badgeIconsData;
    }

    private function _removeOldBadges($userId)
    {
        return UserBadge::where('user_id', '=', $userId)->delete();
    }

    private function _addNewBadges($badges, $userId)
    {
        foreach ($badges as $index => $badge) {
            $badges[$index]['created_at'] = get_created_at();
            $badges[$index]['user_id'] = $userId;
        }

        return UserBadge::insert($badges);
    }

    private function _updateUserBadges($badges, $userId)
    {
        $this->_removeOldBadges($userId);
        return $this->_addNewBadges($badges, $userId);
    }
}
